<?php

namespace App\Controllers\Admin;

use App\Models\Employee;
use App\Models\Hr;

class HrController extends AdminController
{
    public function index(): void
    {
        $this->requirePermission('hr');
        $pageTitle = 'Quản lý lương';
        $month = $_GET['month'] ?? date('Y-m');
        [$from, $to] = $this->monthRange($month);

        $message = null;
        $employees = (new Employee())->getAll();
        $hr = new Hr();

        $leaveSummary = [];
        if (!hr_payroll_upgrade_required()) {
            $leaveSummary = $hr->getLeaveSummaryForEmployees($from, $to);
        }

        $bonusCategories = Hr::bonusCategories();

        $this->adminView('hr/index', [
            'pageTitle'      => $pageTitle,
            'currentRoute'   => 'hr',
            'employees'      => $employees,
            'leaveSummary'   => $leaveSummary,
            'month'          => $month,
            'from'           => $from,
            'to'             => $to,
            'message'        => $message,
            'bonusCategories' => $bonusCategories,
            'readOnly'       => $this->isReadOnly(),
        ], true);
    }

    public function detail(): void
    {
        $this->requirePermission('hr');
        $pageTitle = 'Chi tiết lương';
        $month = $_GET['month'] ?? date('Y-m');
        [$from, $to] = $this->monthRange($month);
        $tab = $_GET['tab'] ?? 'leave';

        $employees = (new Employee())->getAll();
        $employeeId = (int) ($_GET['employee_id'] ?? (empty($employees) ? 0 : $employees[0]['ma_nhan_vien']));

        $hr = new Hr();
        $upgrade = hr_payroll_upgrade_required();

        $leaveRecords       = $upgrade ? [] : $hr->getLeaveRecords($employeeId, $from, $to);
        $bonusPenaltyRecords = $upgrade ? [] : $hr->getBonusPenaltyRecords($employeeId, $from, $to);
        $paymentRecords     = $upgrade ? [] : $hr->getPayments($employeeId, $from, $to);
        $commissionRecords  = $upgrade ? [] : $hr->getCommissionDetails($employeeId, $from, $to);
        $summary            = $upgrade ? [] : $hr->getEmployeeSummary($employeeId, $from, $to);
        $payrollSettings    = $hr->getPayrollSettings($employeeId);
        $bonusCategories    = Hr::bonusCategories();

        // Tìm thông tin nhân viên được chọn
        $employee = null;
        foreach ($employees as $e) {
            if ((int) $e['ma_nhan_vien'] === $employeeId) {
                $employee = $e;
                break;
            }
        }
        if (!$employee && !empty($employees)) {
            $employee = $employees[0];
            $employeeId = (int) $employee['ma_nhan_vien'];
        }

        $this->adminView('hr/detail', [
            'pageTitle'          => $pageTitle,
            'currentRoute'       => 'hr/detail',
            'employees'          => $employees,
            'employee'           => $employee ?? ['ma_nhan_vien' => 0, 'ten' => 'N/A', 'ho_dem' => ''],
            'month'              => $month,
            'from'               => $from,
            'to'                 => $to,
            'tab'                => $tab,
            'leaveRecords'       => $leaveRecords,
            'bonusPenaltyRecords' => $bonusPenaltyRecords,
            'paymentRecords'     => $paymentRecords,
            'commissionRecords'  => $commissionRecords,
            'summary'            => $summary,
            'payrollSettings'    => $payrollSettings,
            'bonusCategories'    => $bonusCategories,
            'readOnly'           => $this->isReadOnly(),
        ], true);
    }

    private function monthRange(string $month): array
    {
        $month = preg_match('/^\d{4}-\d{2}$/', $month) ? $month : date('Y-m');
        $from  = $month . '-01';
        $to    = date('Y-m-t', strtotime($from));

        return [$from, $to];
    }

    /** AJAX: nghỉ, thưởng/phạt, thanh toán lương — route ajax/hr */
    public function ajax(): void
    {
        ob_start();
        $this->requireAuth();

        if (hr_payroll_upgrade_required()) {
            http_response_code(400);
            $this->jsonResponse(['error' => 'Vui lòng import database/upgrade_hr_payroll.sql trước.']);
        }

        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        // Read-only roles chỉ được xem, không được ghi
        $writeActions = ['save_leave', 'save_bonus_penalty', 'save_payment', 'save_settings'];
        if ($this->isReadOnly() && in_array($action, $writeActions, true)) {
            http_response_code(403);
            $this->jsonResponse(['error' => 'Bạn không có quyền thực hiện thao tác này.']);
        }

        try {
            $hr = new Hr();
            switch ($action) {
                case 'save_leave':
                    $hr->addLeave([
                        'ma_nhan_vien'    => (int) ($_POST['employee_id'] ?? $_POST['ma_nhan_vien'] ?? 0),
                        'date_from'       => $_POST['date_from'] ?? date('Y-m-d'),
                        'date_to'         => $_POST['date_to']   ?? date('Y-m-d'),
                        'is_half_day'     => false,
                        'note'            => test_input($_POST['note'] ?? ''),
                        'is_unpaid'       => false,
                        'is_unauthorized' => empty($_POST['is_authorized']),
                    ]);
                    $this->jsonResponse(['success' => true, 'message' => 'Đã ghi nhận ngày nghỉ.']);
                    return;

                case 'save_bonus_penalty':
                    $hr->addBonusPenalty([
                        'ma_nhan_vien' => (int) ($_POST['employee_id'] ?? $_POST['ma_nhan_vien'] ?? 0),
                        'ngay_ghi'     => $_POST['record_date'] ?? date('Y-m-d'),
                        'loai_ghi'     => $_POST['record_type'] ?? 'bonus',
                        'category'     => test_input($_POST['category'] ?? ''),
                        'amount'       => parse_vnd_input($_POST['amount'] ?? 0),
                        'ghi_chu'      => test_input($_POST['note'] ?? ''),
                    ]);
                    $this->jsonResponse(['success' => true, 'message' => 'Đã lưu thưởng/phạt.']);
                    return;

                case 'save_payment':
                    $hr->addPayment([
                        'ma_nhan_vien'    => (int) ($_POST['employee_id'] ?? $_POST['ma_nhan_vien'] ?? 0),
                        'loai_thanh_toan' => $_POST['payment_type']    ?? 'advance',
                        'phuong_thuc'     => test_input($_POST['payment_method'] ?? 'cash'),
                        'amount'          => parse_vnd_input($_POST['amount'] ?? 0),
                        'salary_period'   => test_input($_POST['salary_period'] ?? '') ?: null,
                        'ghi_chu'         => test_input($_POST['note'] ?? ''),
                        'ngay_thanh_toan' => $_POST['payment_date'] ?? date('Y-m-d'),
                    ]);
                    $this->jsonResponse(['success' => true, 'message' => 'Đã ghi nhận thanh toán.']);
                    return;

                case 'save_settings':
                    $hr->savePayrollSettings(
                        (int) ($_POST['employee_id'] ?? $_POST['employee_id'] ?? 0),
                        parse_vnd_input($_POST['base_salary'] ?? 0)
                    );
                    $this->jsonResponse(['success' => true, 'message' => 'Đã lưu cài đặt lương.']);
                    return;

                case 'calc_leave_days':
                    $days = $hr->calculateLeaveDays(
                        $_GET['date_from'] ?? date('Y-m-d'),
                        $_GET['date_to']   ?? date('Y-m-d'),
                        !empty($_GET['is_half_day'])
                    );
                    $this->jsonResponse(['days' => $days]);
                    return;

                default:
                    http_response_code(400);
                    $this->jsonResponse(['error' => 'Hành động không hợp lệ']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }
}

<?php

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;

class BookingController extends AdminController
{
    public function index(): void
    {
        $this->requirePermission('booking');
        $pageTitle = 'Lịch hẹn';
        $view = $_GET['view'] ?? 'custom';
        $date = $_GET['date'] ?? date('Y-m-d');

        // Nếu người dùng chọn khoảng ngày thủ công (date + date_to)
        $dateTo = $_GET['date_to'] ?? '';

        if ($dateTo !== '' && $dateTo >= $date) {
            // Chế độ custom: dùng đúng khoảng người dùng chọn
            $from = $date;
            $to   = $dateTo;
            $view = 'custom';
        } elseif ($view === 'week') {
            $from = $date;
            $to   = date('Y-m-d', strtotime($date . ' +6 days'));
        } elseif ($view === 'month') {
            $from = date('Y-m-01', strtotime($date));
            $to   = date('Y-m-t', strtotime($date));
        } else {
            // day hoặc mặc định
            $from = $date;
            $to   = $date;
            $view = 'day';
        }

        $appointmentModel = new Appointment();
        $appointments = $appointmentModel->getByDateRange($from, $to);
        foreach ($appointments as &$row) {
            $services = $appointmentModel->getBookedServicesDetailed((int) $row['ma_lich_hen']);
            $row['booked_services'] = $services;
            $row['services_text'] = implode(', ', array_column($services, 'ten_dich_vu'));
            $row['services_total'] = array_sum(array_map(static fn ($s) => (float) $s['gia'], $services));
            $row['has_paid_order'] = salon_upgrade_required() ? false : $appointmentModel->hasPaidOrder((int) $row['ma_lich_hen']);
        }
        unset($row);
        $pendingReminders = [];

        $this->adminView('booking/index', compact(
            'pageTitle', 'view', 'date', 'from', 'to', 'appointments', 'pendingReminders'
        ), true);
    }

    public function create(): void
    {
        $this->requirePermission('booking');
        $pageTitle = 'Tạo lịch hẹn';
        $message = null;

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_booking'])) {
            try {
                $appointmentModel = new Appointment();
                $clientId = (int) ($_POST['ma_khach_hang'] ?? $_POST['client_id'] ?? 0);

                if ($clientId <= 0) {
                    $name  = trim($_POST['client_name'] ?? '');
                    $phone = trim($_POST['client_phone'] ?? '');
                    if ($phone === '' && $name === '') {
                        throw new \RuntimeException('Vui lòng chọn khách hàng hoặc nhập số điện thoại / tên để tạo mới.');
                    }
                    // Tạo khách mới từ tên + SĐT
                    $parts     = preg_split('/\s+/u', $name ?: 'Khách Vãng Lai', 2);
                    $firstName = $parts[0];
                    $lastName  = $parts[1] ?? '';
                    $clientId  = (new Client())->quickCreate($firstName, $lastName, $phone ?: '0000000000');
                }

                if ($clientId <= 0) {
                    throw new \RuntimeException('Không xác định được khách hàng. Vui lòng chọn lại.');
                }

                $guestGroups = $this->parseGuestGroups($_POST);
                if ($guestGroups === []) {
                    throw new \RuntimeException('Vui lòng chọn ít nhất một dịch vụ cho khách.');
                }

                $baseData = [
                    'ma_khach_hang'     => $clientId ?: null,
                    'thoi_gian_bat_dau' => $_POST['start_time'],
                    'trang_thai'        => $_POST['status'] ?? 'confirmed',
                    'nguon_dat'         => $_POST['booking_source'] ?? 'hotline',
                ];

                $newIds = $appointmentModel->createFromGuestGroups($baseData, $guestGroups);
                $newId = $newIds[0] ?? 0;

                // Gửi email xác nhận cho khách hàng (nếu có email)
                if ($newId > 0) {
                    try {
                        $client = (new Client())->find($clientId);
                        $clientEmail = $client['email'] ?? '';
                        // Bỏ email giả (tạo tự động khi không có email thật)
                        if (!empty($clientEmail) && !str_contains($clientEmail, '@salon.local')) {
                            $clientName = trim(($client['ten'] ?? '') . ' ' . ($client['ho_dem'] ?? ''));
                            // Lấy thông tin dịch vụ và nhân viên từ tất cả nhóm
                            $allServiceIds = array_merge(...array_column($guestGroups, 'service_ids'));
                            $svcModel = new \App\Models\Service();
                            $svcDetails = $svcModel->getByIds(array_unique($allServiceIds));
                            // Lấy tên NV đầu tiên đại diện
                            $firstEmpId = $guestGroups[0]['ma_nhan_vien'] ?? 0;
                            $empObj = $firstEmpId > 0 ? (new Employee())->find($firstEmpId) : null;
                            $empName = $empObj ? trim(($empObj['ten'] ?? '') . ' ' . ($empObj['ho_dem'] ?? '')) : 'Barber';
                            (new \MailService())->sendBookingConfirmation($clientEmail, $clientName, [
                                'start_time'  => $_POST['start_time'],
                                'barber_name' => $empName,
                                'services'    => array_map(fn ($s) => [
                                    'name'  => $s['ten_dich_vu'],
                                    'price' => $s['gia'],
                                ], $svcDetails),
                            ]);
                        }
                    } catch (\Throwable $mailErr) {
                        error_log('Admin booking email error: ' . $mailErr->getMessage());
                    }
                }

                header('Location: index.php?route=booking&date=' . urlencode(date('Y-m-d', strtotime($_POST['start_time']))) . '&msg=created');
                exit;
            } catch (\Throwable $e) {
                $message = ['type' => 'danger', 'text' => $e->getMessage()];
            }
        }

        $categories = (new \App\Models\ServiceCategory())->getAllWithServices();
        $categoriesJson = [];
        foreach ($categories as $cat) {
            $services = [];
            foreach ($cat['services'] ?? [] as $s) {
                $services[] = [
                    'id' => (int) $s['ma_dich_vu'],
                    'name' => $s['ten_dich_vu'],
                    'price' => (float) $s['gia'],
                    'duration' => (int) $s['thoi_luong'],
                ];
            }
            $categoriesJson[] = [
                'id' => (int) $cat['ma_danh_muc'],
                'name' => $cat['ten_danh_muc'],
                'services' => $services,
            ];
        }

        $employeesJson = array_map(static fn ($e) => [
            'id' => (int) $e['ma_nhan_vien'],
            'name' => trim($e['ten'] . ' ' . $e['ho_dem']),
        ], (new Employee())->getAll());

        // Hỗ trợ cả ?ma_khach_hang= và ?client_id= (từ trang CRM)
        $preselectClientId = (int) ($_GET['ma_khach_hang'] ?? $_GET['client_id'] ?? 0);
        $preselectClient = $preselectClientId > 0 ? (new Client())->find($preselectClientId) : null;

        $this->adminView('booking/create', [
            'pageTitle' => $pageTitle,
            'employees' => (new Employee())->getAll(),
            'categoriesJson' => $categoriesJson,
            'employeesJson' => $employeesJson,
            'message' => $message,
            'preselectClientId' => $preselectClientId,
            'preselectClient' => $preselectClient,
        ], true);
    }

    /** @return array<int, array{ma_nhan_vien: int, service_ids: int[]}> */
    private function parseGuestGroups(array $post): array
    {
        $groups = [];
        $guestData = $post['guest'] ?? [];

        if (!is_array($guestData)) {
            return [];
        }

        foreach ($guestData as $guestIndex => $guest) {
            $rows = $guest['rows'] ?? [];
            if (!is_array($rows)) {
                continue;
            }

            $byEmployee = [];
            foreach ($rows as $row) {
                
                $serviceId  = (int) ($row['service_id']  ?? $row['ma_dich_vu']   ?? 0);
                $employeeId = (int) ($row['employee_id'] ?? $row['ma_nhan_vien'] ?? 0);
                if ($serviceId <= 0 || $employeeId <= 0) {
                    continue;
                }
                $byEmployee[$employeeId][] = $serviceId;
            }

            foreach ($byEmployee as $employeeId => $serviceIds) {
                $groups[] = [
                    'ma_nhan_vien' => (int) $employeeId,
                    'service_ids' => array_values(array_unique($serviceIds)),
                ];
            }
        }

        return $groups;
    }

    public function edit(): void
    {
        $this->requirePermission('booking');
        $pageTitle = 'Sửa lịch hẹn';
        $id = (int) ($_GET['id'] ?? 0);
        $appointmentModel = new Appointment();
        $appointment = $appointmentModel->getById($id);

        if (!$appointment) {
            $this->redirect(admin_route('booking'));
        }

        $message = null;
        $isPaid  = $appointmentModel->hasPaidOrder($id);

        // Normalize status
        $st = strtolower(trim($appointment['trang_thai'] ?? 'confirmed'));
        if (in_array($st, ['pending', 'confirmed', ''])) $st = 'confirmed';
        elseif (in_array($st, ['arrived', 'in_service', 'check_in', 'checkin'])) $st = 'check_in';
        elseif (in_array($st, ['completed', 'check_out', 'checkout'])) $st = 'check_out';
        $isLocked = $isPaid || $st === 'check_out';

        if (!$isLocked && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['update_booking'])) {
            try {
                // Cập nhật trạng thái
                if (!empty($_POST['status'])) {
                    $appointmentModel->updateStatus($id, $_POST['status']);
                }

                // Cập nhật thời gian nếu có
                if (!empty($_POST['start_time'])) {
                    $appointmentModel->updateTime(
                        $id,
                        $_POST['start_time'],
                        $_POST['end_time'] ?? ''
                    );
                }

                // Cập nhật nhân viên nếu có
                if (!empty($_POST['ma_nhan_vien'])) {
                    $appointmentModel->updateEmployee($id, (int) $_POST['ma_nhan_vien']);
                }

                $message = ['type' => 'success', 'text' => 'Đã cập nhật lịch hẹn.'];
                $appointment = $appointmentModel->getById($id);
                $isPaid = $appointmentModel->hasPaidOrder($id);
                // Re-normalize
                $st = strtolower(trim($appointment['trang_thai'] ?? 'confirmed'));
                if (in_array($st, ['pending', 'confirmed', ''])) $st = 'confirmed';
                elseif (in_array($st, ['arrived', 'in_service', 'check_in', 'checkin'])) $st = 'check_in';
                elseif (in_array($st, ['completed', 'check_out', 'checkout'])) $st = 'check_out';
                $isLocked = $isPaid || $st === 'check_out';
            } catch (\Throwable $e) {
                $message = ['type' => 'danger', 'text' => $e->getMessage()];
            }
        }

        $services  = $appointmentModel->getBookedServicesDetailed($id);
        $employees = (new Employee())->getAll();

        $this->adminView('booking/edit', compact(
            'pageTitle', 'appointment', 'services', 'employees',
            'isPaid', 'isLocked', 'st', 'message'
        ), true);
    }

    public function updateStatus(): void
    {
        $this->requireAuth();
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $id     = (int) ($_POST['ma_lich_hen'] ?? $_POST['appointment_id'] ?? 0);
            $status = $_POST['status'] ?? 'confirmed';

            $map = [
                'pending'    => 'confirmed',
                'arrived'    => 'check_in',
                'in_service' => 'check_in',
                'completed'  => 'check_out',
            ];
            $status = $map[$status] ?? $status;

            if ($id > 0) {
                (new Appointment())->updateStatus($id, $status);
            }
        }

        $redirectDate   = $_POST['redirect_date']    ?? date('Y-m-d');
        $redirectDateTo = $_POST['redirect_date_to'] ?? '';
        $params = ['date' => $redirectDate];
        if ($redirectDateTo !== '' && $redirectDateTo !== $redirectDate) {
            $params['date_to'] = $redirectDateTo;
        }
        header('Location: index.php?route=booking&' . http_build_query($params));
        exit;
    }

    /** AJAX: hủy lịch hẹn — route ajax/appointments */
    public function ajax(): void
    {
        ob_start();
        $this->requireAuth();

        if (($_POST['do'] ?? '') === 'Cancel Appointment') {
            $id     = (int) ($_POST['appointment_id'] ?? 0);
            $reason = test_input($_POST['cancellation_reason'] ?? '');
            $appointmentModel = new Appointment();

            // Lấy thông tin lịch hẹn trước khi hủy để gửi email
            $appt = $appointmentModel->getById($id);
            $appointmentModel->cancel($id, $reason);

            // Gửi email thông báo hủy cho khách hàng
            if ($appt && !empty($appt['ma_khach_hang'])) {
                try {
                    $client = (new Client())->find((int) $appt['ma_khach_hang']);
                    $clientEmail = $client['email'] ?? '';
                    if (!empty($clientEmail) && !str_contains($clientEmail, '@salon.local')) {
                        $clientName = trim(($client['ten'] ?? '') . ' ' . ($client['ho_dem'] ?? ''));
                        (new \MailService())->sendBookingCancellation($clientEmail, $clientName, [
                            'start_time'    => $appt['thoi_gian_bat_dau'] ?? '',
                            'cancel_reason' => $reason,
                        ]);
                    }
                } catch (\Throwable $mailErr) {
                    error_log('Cancel email error: ' . $mailErr->getMessage());
                }
            }

            $this->jsonResponse(['success' => true]);
        }

        $this->jsonResponse(['error' => 'Hành động không hợp lệ']);
    }

    public function sendReminders(): void
    {
        $this->requireAuth();
        header('Location: index.php?route=booking&msg=reminders');
        exit;
    }
}

<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use Exception;

class AppointmentController extends Controller
{
    public function index(): void
    {
        $message = null;
        $messageType = null;

        if (
            isset($_POST['submit_book_appointment_form'])
            && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
        ) {
            try {
                $selectedServices = $_POST['selected_services'] ?? [];
                $selectedEmployee = (int) ($_POST['selected_employee'] ?? 0);
                $selectedDateTime = explode(' ', $_POST['desired_date_time'] ?? '');

                if (count($selectedDateTime) < 3) {
                    throw new Exception('Thời gian đặt lịch không hợp lệ. Vui lòng chọn lại.');
                }

                $dateSelected = $selectedDateTime[0];
                $startTime = $dateSelected . ' ' . $selectedDateTime[1];
                $endTime = $dateSelected . ' ' . $selectedDateTime[2];

                $appointmentModel = new Appointment();
                $appointmentModel->book(
                    $selectedServices,
                    $selectedEmployee,
                    $startTime,
                    $endTime,
                    test_input($_POST['client_first_name'] ?? ''),
                    test_input($_POST['client_last_name'] ?? ''),
                    test_input($_POST['client_phone_number'] ?? ''),
                    test_input($_POST['client_email'] ?? '')
                );

                // Gửi email xác nhận cho khách hàng
                $clientEmail = trim(test_input($_POST['client_email'] ?? ''));
                if ($clientEmail !== '') {
                    try {
                        $emp = (new Employee())->find($selectedEmployee);
                        $empName = $emp ? trim(($emp['ten'] ?? '') . ' ' . ($emp['ho_dem'] ?? '')) : 'Barber';
                        $svcIds = array_map('intval', (array) $selectedServices);
                        $svcDetails = !empty($svcIds) ? (new Service())->getByIds($svcIds) : [];
                        $clientName = trim(
                            test_input($_POST['client_first_name'] ?? '') . ' ' .
                            test_input($_POST['client_last_name'] ?? '')
                        );
                        (new \MailService())->sendBookingConfirmation($clientEmail, $clientName ?: 'Quý khách', [
                            'start_time'  => $startTime,
                            'barber_name' => $empName,
                            'services'    => array_map(fn ($s) => [
                                'name'  => $s['ten_dich_vu'],
                                'price' => $s['gia'],
                            ], $svcDetails),
                        ]);
                    } catch (\Throwable $mailErr) {
                        // Gửi mail thất bại không chặn luồng đặt lịch
                        error_log('[BookingEmail] ' . $mailErr->getMessage() . ' | File: ' . $mailErr->getFile() . ':' . $mailErr->getLine());
                    }
                }

                $message = 'Đặt lịch thành công! Chúng tôi sẽ liên hệ xác nhận sớm nhất.';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = $e->getMessage();
                $messageType = 'danger';
            }
        }

        $serviceModel = new Service();
        $employeeModel = new Employee();

        $this->view('appointment/index', [
            'servicesByCategory' => $serviceModel->getGroupedByCategory(),
            'employees'          => $employeeModel->getBarbers(),
            'message'            => $message,
            'messageType'        => $messageType,
        ]);
    }

    public function calendar(): void
    {
        if (!isset($_POST['selected_employee'], $_POST['selected_services'])) {
            $this->redirect('');
            return;
        }

        $employeeId = (int) $_POST['selected_employee'];
        $serviceIds = $_POST['selected_services'];

        $appointmentModel = new Appointment();
        $days = $appointmentModel->getCalendarSlots($employeeId, $serviceIds);

        $this->view('appointment/calendar', ['days' => $days], null);
    }

    /** AJAX: tra cứu khách hàng theo số điện thoại */
    public function lookupClient(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $phone = trim($_POST['phone'] ?? '');

        if (!preg_match('/^\d{10}$/', $phone)) {
            echo json_encode(['found' => false], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $client = (new \App\Models\Client())->findByPhone($phone);
        if (!$client) {
            echo json_encode(['found' => false], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'found'    => true,
            'ho'       => $client['ho_dem'] ?? '',
            'ten'      => $client['ten'] ?? '',
            'email'    => $client['email'] ?? '',
            'phone'    => $client['so_dien_thoai'] ?? '',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** AJAX: trả về thợ ít lịch nhất dạng JSON */
    public function randomStaff(): void
    {
        $employeeModel = new Employee();
        $id = $employeeModel->getLeastBusyBarber();

        header('Content-Type: application/json; charset=utf-8');
        if ($id === null) {
            echo json_encode(['error' => 'Không có thợ nào khả dụng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Lấy thông tin nhân viên
        $emp = $employeeModel->find($id);
        echo json_encode([
            'ma_nhan_vien' => $id,
            'ten'          => $emp['ten'] ?? '',
            'ho_dem'       => $emp['ho_dem'] ?? '',
            'chuc_vu'      => $emp['chuc_vu'] ?? '',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

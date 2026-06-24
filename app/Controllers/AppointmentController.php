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
            'employees' => $employeeModel->getAll(),
            'message' => $message,
            'messageType' => $messageType,
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
}

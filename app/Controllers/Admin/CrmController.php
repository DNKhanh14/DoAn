<?php

namespace App\Controllers\Admin;

use App\Models\Client;

class CrmController extends AdminController
{
    public function index(): void
    {
        $pageTitle = 'Khách hàng';
        $searchQuery = trim($_GET['q'] ?? '');
        $message = null;

        // Xử lý xóa khách từ danh sách
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['delete_client_id'])) {
            $delId = (int) $_POST['delete_client_id'];
            (new Client())->delete($delId);
            header('Location: ' . admin_route('crm', ['msg' => 'deleted']));
            exit;
        }

        $clients = $searchQuery !== ''
            ? (new Client())->search($searchQuery, 100)
            : (new Client())->getAll();

        $this->adminView('crm/index', compact('pageTitle', 'clients', 'searchQuery'));
    }

    public function detail(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? $_POST['ma_khach_hang'] ?? 0);
        $clientModel = new Client();
        $client = $clientModel->find($id);

        if (!$client) {
            $this->redirect(admin_route('crm'));
        }

        $pageTitle = 'Hồ sơ khách: ' . trim($client['ten'] . ' ' . $client['ho_dem']);
        $message = null;
        $error = null;

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if (isset($_POST['save_client'])) {
                $first = test_input($_POST['first_name'] ?? '');
                $last = test_input($_POST['last_name'] ?? '');
                $phone = test_input($_POST['phone_number'] ?? '');
                if ($first === '' || $phone === '') {
                    $error = 'Họ/tên và số điện thoại là bắt buộc.';
                } else {
                    $clientModel->updateBasic($id, $first, $last, $phone);
                    $message = 'Đã lưu thông tin khách.';
                    $client = $clientModel->find($id);
                    $pageTitle = 'Hồ sơ khách: ' . trim($client['ten'] . ' ' . $client['ho_dem']);
                }
            }
        }

        $orderModel = new \App\Models\Order();
        $orderHistory = $orderModel->getByClient($id);
        $visitCount   = $orderModel->countServiceVisitsByClient($id);

        $this->adminView('crm/detail', [
            'pageTitle'    => $pageTitle,
            'client'       => $client,
            'message'      => $message,
            'error'        => $error,
            'orderHistory' => $orderHistory,
            'visitCount'   => $visitCount,
        ], true);
    }

    public function birthdays(): void
    {
        $pageTitle = 'Marketing - Sinh nhật tháng này';
        $clients = salon_upgrade_required() ? [] : (new Client())->getBirthdaysThisMonth();

        $this->adminView('crm/birthdays', [
            'pageTitle' => $pageTitle,
            'clients' => $clients,
            'logs' => [],
        ]);
    }
}

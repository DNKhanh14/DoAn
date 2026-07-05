<?php

namespace App\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;

class PosController extends AdminController
{
    // ─── Routes: GET/POST pos, GET pos/orders, GET pos/print, POST ajax/pos ─

    public function index(): void
    {
        $this->requirePermission('pos');
        $pageTitle = 'Thu ngân';
        $upgradeRequired = salon_upgrade_required();
        $employees       = (new Employee())->getAll();

        $prefill       = null;
        $appointmentId = (int) ($_GET['appointment_id'] ?? 0);
        if (!$upgradeRequired && $appointmentId > 0) {
            $prefill = (new Appointment())->getPosPrefill($appointmentId);
        }

        $this->adminView('pos/index', [
            'pageTitle'       => $pageTitle,
            'upgradeRequired' => $upgradeRequired,
            'servicesGrouped' => $upgradeRequired ? [] : (new Service())->getGroupedByCategory(),
            'products'        => $upgradeRequired ? [] : (new Product())->getAll(),
            'employees'       => $employees,
            'clients'         => (new Client())->getAll(),
            'defaultEmployeeId' => !empty($employees) ? (int) $employees[0]['ma_nhan_vien'] : 0,
            'draftOrderCode'  => $upgradeRequired ? '' : (new Order())->generateOrderCode(),
            'prefill'         => $prefill,
            'extraJs'         => 'Design/js/pos.js?v=' . (@filemtime(ADMIN_PATH . '/Design/js/pos.js') ?: time()),
        ], true);
    }

    public function orders(): void
    {
        $this->requirePermission('pos');
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(5, min(100, (int) ($_GET['per_page'] ?? 20)));

        $orderModel = new Order();
        $totalItems = salon_upgrade_required() ? 0 : $orderModel->countAll();
        $pag        = paginate($totalItems, $page, $perPage);
        $pag['total'] = $totalItems;
        $orders     = salon_upgrade_required() ? [] : $orderModel->getPaginated($pag['offset'], $perPage);
        $baseUrl    = admin_route('pos/orders');

        $this->adminView('pos/orders', compact('orders', 'pag', 'baseUrl') + [
            'pageTitle' => 'Danh sách đơn hàng',
        ], true);
    }

    public function deleteOrder(): void
    {
        $this->requirePermission('pos');
        $id = (int) ($_POST['ma_don_hang'] ?? 0);
        if ($id > 0) {
            (new Order())->delete($id);
            $_SESSION['flash_success'] = 'Đã xóa hóa đơn thành công.';
        }
        header('Location: ' . admin_route('pos/orders'));
        exit;
    }

    public function editOrder(): void
    {
        $this->requirePermission('pos');
        $id    = (int) ($_GET['id'] ?? $_POST['ma_don_hang'] ?? 0);
        $order = (new Order())->find($id);
        if (!$order) {
            $_SESSION['flash_error'] = 'Không tìm thấy hóa đơn.';
            header('Location: ' . admin_route('pos/orders'));
            exit;
        }

        $message = null;
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_order'])) {
            $note   = test_input($_POST['ghi_chu'] ?? '');
            $method = $_POST['phuong_thuc_thanh_toan'] ?? 'cash';
            (new Order())->updateNote($id, $note, $method);
            $_SESSION['flash_success'] = 'Đã cập nhật hóa đơn.';
            header('Location: ' . admin_route('pos/orders'));
            exit;
        }

        $items = (new Order())->getItems($id);
        $this->adminView('pos/edit_order', [
            'pageTitle' => 'Sửa hóa đơn ' . ('#' . $id),
            'order'     => $order,
            'items'     => $items,
        ], true);
    }

    public function detail(): void
    {
        $this->requirePermission('pos');
        $id    = (int) ($_GET['id'] ?? 0);
        $order = (new Order())->find($id);
        if (!$order) {
            $_SESSION['flash_error'] = 'Không tìm thấy hóa đơn.';
            header('Location: ' . admin_route('pos/orders'));
            exit;
        }
        $items = (new Order())->getItems($id);
        $orderCode = $order['ma_don'] ?? ('HD' . str_pad((string) $id, 6, '0', STR_PAD_LEFT));
        $this->adminView('pos/detail', [
            'pageTitle' => 'Chi tiết hóa đơn ' . $orderCode,
            'order'     => $order,
            'items'     => $items,
        ], true);
    }

    public function print(): void
    {
        $this->requireAuth();
        $id    = (int) ($_GET['id'] ?? 0);
        $order = (new Order())->find($id);
        if (!$order) { echo 'Không tìm thấy hóa đơn'; return; }
        $items = (new Order())->getItems($id);
        require APP_PATH . '/Views/admin/pos/print.php';
    }

    /** AJAX: search_clients, quick_client, checkout — route ajax/pos */
    public function ajax(): void
    {
        ob_start();
        $this->requireAuth();

        $raw = json_decode(file_get_contents('php://input') ?: '{}', true);
        if (!is_array($raw)) { $raw = []; }
        $action = $_POST['action'] ?? $_GET['action'] ?? ($raw['action'] ?? '');

        try {
            switch ($action) {
                case 'search_clients':
                    $list = array_map(static fn ($c) => [
                        'client_id'    => $c['ma_khach_hang'],
                        'first_name'   => $c['ten'],
                        'last_name'    => $c['ho_dem'],
                        'phone_number' => $c['so_dien_thoai'] ?? '',
                        'email'        => $c['email'] ?? '',
                    ], (new Client())->search((string) ($_GET['q'] ?? ''), 15));
                    $this->jsonResponse(['clients' => $list]);
                    return;

                case 'quick_client':
                    $newId = (new Client())->quickCreate(
                        test_input($_POST['first_name'] ?? 'Khách'),
                        test_input($_POST['last_name']  ?? 'Mới'),
                        test_input($_POST['phone']  ?? ''),
                        test_input($_POST['email']  ?? '')
                    );
                    $raw2 = (new Client())->find($newId);
                    $this->jsonResponse(['success' => true, 'client' => [
                        'client_id'    => $raw2['ma_khach_hang'],
                        'first_name'   => $raw2['ten'],
                        'last_name'    => $raw2['ho_dem'],
                        'phone_number' => $raw2['so_dien_thoai'] ?? '',
                    ]]);
                    return;

                case 'checkout':
                    $orderId = $this->processCheckout(array_merge($_POST, $raw));
                    $order   = (new Order())->find($orderId);
                    $this->jsonResponse([
                        'success'    => true,
                        'order_id'   => $orderId,
                        'order_code' => $order['ma_don'] ?? ('HD' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT)),
                        'total'      => (float) $order['tong_cong'],
                    ]);
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

    // ── Private helpers ──────────────────────────────────────────────────────

    private function processCheckout(array $data): int
    {
        if (salon_upgrade_required()) {
            throw new \RuntimeException('Vui lòng import database/upgrade_salon_features.sql trước khi dùng Thu ngân.');
        }

        $lines = $data['lines'] ?? [];
        if (empty($lines)) {
            throw new \RuntimeException('Hóa đơn chưa có dịch vụ hoặc sản phẩm.');
        }

        $items      = [];
        $subtotal   = 0;
        $svcModel   = new Service();
        $prodModel  = new Product();

        foreach ($lines as $line) {
            $type         = $line['type']       ?? 'service';
            $refId        = (int) ($line['ref_id']    ?? 0);
            $qty          = max(1, (int) ($line['qty'] ?? 1));
            $unitPrice    = (float) ($line['unit_price']  ?? 0);
            $discount     = (float) ($line['discount']    ?? 0);
            $discPct      = !empty($line['discount_percent']);

            if ($type === 'service') {
                $s = $svcModel->find($refId);
                if (!$s) continue;
                $name      = $s['ten_dich_vu'];
                $unitPrice = $unitPrice > 0 ? $unitPrice : (float) $s['gia'];
            } else {
                $p = $prodModel->find($refId);
                if (!$p) continue;
                $name      = $p['ten_san_pham'];
                $unitPrice = $unitPrice > 0 ? $unitPrice : (float) $p['gia_ban'];
                if ((float) $p['so_luong_ton'] < $qty) {
                    throw new \RuntimeException('Sản phẩm "' . $name . '" không đủ tồn kho.');
                }
            }

            $lineSub   = $unitPrice * $qty;
            $lineDisc  = $discPct ? ($lineSub * $discount / 100) : $discount;
            $lineTotal = max(0, $lineSub - $lineDisc);
            $subtotal += $lineTotal;   // cộng 1 lần duy nhất cho tổng hóa đơn

            // Lấy danh sách NV — ưu tiên employee_ids (nhiều NV), fallback employee_id
            $empIds = [];
            if (!empty($line['employee_ids']) && is_array($line['employee_ids'])) {
                $empIds = array_values(array_filter(array_map('intval', $line['employee_ids'])));
            }
            if (empty($empIds) && !empty($line['employee_id'])) {
                $empIds = [(int) $line['employee_id']];
            }

            if (count($empIds) <= 1) {
                // 1 NV hoặc không có → 1 dòng bình thường
                $items[] = [
                    'item_type'           => $type,
                    'ref_id'              => $refId,
                    'item_name'           => $name,
                    'quantity'            => $qty,
                    'unit_price'          => $unitPrice,
                    'line_total'          => $lineTotal,
                    'ma_nhan_vien'        => $empIds[0] ?? null,
                    'line_discount'       => $lineDisc,
                    'discount_is_percent' => $discPct,
                ];
            } else {
                // Nhiều NV → tách thành N dòng, chia đều giá trị
                $n            = count($empIds);
                $splitPrice   = round($unitPrice / $n, 2);
                $splitDisc    = round($lineDisc  / $n, 2);
                $splitTotal   = round($lineTotal  / $n, 2);

                foreach ($empIds as $i => $empId) {
                    // Dòng cuối nhận phần còn lại (tránh sai số làm tròn)
                    $isLast    = ($i === $n - 1);
                    $thisPrice = $isLast ? ($unitPrice - $splitPrice * ($n - 1)) : $splitPrice;
                    $thisDisc  = $isLast ? ($lineDisc  - $splitDisc  * ($n - 1)) : $splitDisc;
                    $thisTotal = $isLast ? ($lineTotal  - $splitTotal * ($n - 1)) : $splitTotal;

                    $items[] = [
                        'item_type'           => $type,
                        'ref_id'              => $refId,
                        'item_name'           => $name,
                        'quantity'            => $qty,
                        'unit_price'          => $thisPrice,
                        'line_total'          => max(0, $thisTotal),
                        'ma_nhan_vien'        => $empId,
                        'line_discount'       => $thisDisc,
                        'discount_is_percent' => false,
                    ];
                }
            }
        }

        $invDisc    = (float) ($data['invoice_discount'] ?? 0);
        $invDiscPct = !empty($data['invoice_discount_percent']);
        if ($invDiscPct) { $invDisc = $subtotal * $invDisc / 100; }

        $total        = max(0, $subtotal - $invDisc);
        $clientId     = (int) ($data['client_id'] ?? 0);
        $appointmentId = (int) ($data['appointment_id'] ?? $data['ma_lich_hen'] ?? 0);

        $orderId = (new Order())->create([
            'ma_khach_hang'          => $clientId ?: null,
            'ma_lich_hen'            => $appointmentId ?: null,
            'tong_truoc_giam'        => $subtotal,
            'giam_gia'               => $invDisc,
            'tong_cong'              => $total,
            'phuong_thuc_thanh_toan' => $data['payment_method'] ?? 'cash',
            'ghi_chu'                => test_input($data['note'] ?? ''),
            
        ], $items);

        if ($appointmentId > 0) {
            // Cập nhật lịch hẹn về check_out sau khi thanh toán
            (new Appointment())->updateStatus($appointmentId, 'check_out');
        }

        return $orderId;
    }
}

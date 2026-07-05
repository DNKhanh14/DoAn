<?php

namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Service;

class InventoryController extends AdminController
{
    public function index(): void
    {
        $this->requirePermission('inventory');
        $pageTitle   = 'Kho hàng';
        $productModel = new Product();
        $message      = null;
        $searchQuery  = trim($_GET['q'] ?? '');
        $page         = max(1, (int) ($_GET['page'] ?? 1));
        $perPage      = max(5, min(100, (int) ($_GET['per_page'] ?? 20)));

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $this->requireWritePermission();
            if (isset($_POST['save_product'])) {
                $productModel->save([
                    'ten_san_pham'       => test_input($_POST['ten_san_pham']),
                    'don_vi'             => test_input($_POST['don_vi'] ?? 'cai'),
                    'gia_ban'            => parse_vnd_input($_POST['gia_ban'] ?? 0),
                    'gia_nhap'           => parse_vnd_input($_POST['gia_nhap'] ?? 0),
                    'so_luong_ton'       => (int) ($_POST['so_luong_ton'] ?? 0),
                    'so_luong_toi_thieu' => (int) ($_POST['so_luong_toi_thieu'] ?? 0),
                    'hoat_dong'          => isset($_POST['is_active']) ? 1 : 0,
                ], !empty($_POST['ma_san_pham']) ? (int) $_POST['ma_san_pham'] : null);
                $message = 'Đã lưu sản phẩm.';
            } elseif (isset($_POST['delete_product'])) {
                $productModel->delete((int) $_POST['ma_san_pham']);
                $message = 'Đã xóa sản phẩm.';
            }
        }

        $totalItems  = salon_upgrade_required() ? 0 : $productModel->count($searchQuery);
        $pag         = paginate($totalItems, $page, $perPage);
        $pag['total'] = $totalItems;
        $products    = salon_upgrade_required() ? [] : $productModel->getPaginated($pag['offset'], $perPage, $searchQuery);
        $baseUrl     = admin_route('inventory') . ($searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '');

        $this->adminView('inventory/index', [
            'pageTitle'   => $pageTitle,
            'products'    => $products,
            'lowStock'    => salon_upgrade_required() ? [] : $productModel->getLowStock(),
            'services'    => (new Service())->getAll(),
            'message'     => $message,
            'searchQuery' => $searchQuery,
            'pag'         => $pag,
            'baseUrl'     => $baseUrl,
            'readOnly'    => $this->isReadOnly(),
        ], true);
    }
}

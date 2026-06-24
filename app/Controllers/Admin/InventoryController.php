<?php

namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Service;

class InventoryController extends AdminController
{
    public function index(): void
    {
        $pageTitle = 'Kho hàng';
        $productModel = new Product();
        $message = null;
        $searchQuery = trim($_GET['q'] ?? '');

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if (isset($_POST['save_product'])) {
                $productModel->save([
                    'ten_san_pham' => test_input($_POST['ten_san_pham']),
                    'ma_sku' => test_input($_POST['ma_sku'] ?? ''),
                    'don_vi' => test_input($_POST['don_vi'] ?? 'cai'),
                    'gia_ban' => parse_vnd_input($_POST['gia_ban'] ?? 0),
                    'gia_nhap' => parse_vnd_input($_POST['gia_nhap'] ?? 0),
                    'so_luong_ton' => (int) ($_POST['so_luong_ton'] ?? 0),
                    'so_luong_toi_thieu' => (int) ($_POST['so_luong_toi_thieu'] ?? 0),
                    'hoat_dong' => isset($_POST['is_active']) ? 1 : 0,
                ], !empty($_POST['ma_san_pham']) ? (int) $_POST['ma_san_pham'] : null);
                $message = 'Đã lưu sản phẩm.';
            } elseif (isset($_POST['delete_product'])) {
                $productModel->delete((int) $_POST['ma_san_pham']);
                $message = 'Đã xóa sản phẩm.';
            }
        }

        $this->adminView('inventory/index', [
            'pageTitle' => $pageTitle,
            'products' => salon_upgrade_required() ? [] : ($searchQuery !== '' ? $productModel->search($searchQuery) : $productModel->getAll()),
            'lowStock' => salon_upgrade_required() ? [] : $productModel->getLowStock(),
            'services' => (new Service())->getAll(),
            'message' => $message,
            'searchQuery' => $searchQuery,
        ], true);
    }
}

<?php

namespace App\Controllers\Admin;

use App\Models\ServiceCategory;
use Exception;

class ServiceCategoriesController extends AdminController
{
    // ─── Routes: GET service-categories, POST ajax/service-categories ───────

    public function index(): void
    {
        $this->requirePermission('services');
        $pageTitle = 'Danh mục dịch vụ';
        $model     = new ServiceCategory();

        $this->adminView('service-categories/index', [
            'pageTitle'  => $pageTitle,
            'categories' => array_values(array_filter(
                $model->getAll(),
                static fn ($c) => strtolower(trim($c['ten_danh_muc'] ?? '')) !== 'uncategorized'
            )),
        ]);
    }

    /** AJAX: thêm / sửa / xóa danh mục — route ajax/service-categories */
    public function ajax(): void
    {
        ob_start();
        $this->requireAuth();

        $do     = $_POST['do']     ?? '';
        $action = $_POST['action'] ?? '';

        if ($do === 'Add') {
            $this->ajaxAdd();
        } elseif ($action === 'Delete') {
            $this->ajaxDelete();
        } elseif ($action === 'Edit') {
            $this->ajaxEdit();
        } else {
            $this->jsonResponse(['alert' => 'Warning', 'message' => 'Hành động không hợp lệ.']);
        }
    }

    // ── Private AJAX helpers ─────────────────────────────────────────────────

    private function ajaxAdd(): void
    {
        $name = test_input($_POST['category_name'] ?? '');

        if (checkItem('ten_danh_muc', 'danh_muc_dich_vu', $name) !== 0) {
            $this->jsonResponse(['alert' => 'Warning', 'message' => 'Tên danh mục này đã tồn tại!']);
            return;
        }

        (new ServiceCategory())->create($name);
        $this->jsonResponse(['alert' => 'Success', 'message' => 'Đã thêm danh mục mới thành công!']);
    }

    private function ajaxDelete(): void
    {
        $id = (int) ($_POST['category_id'] ?? 0);
        try {
            (new ServiceCategory())->delete($id);
            $this->jsonResponse(['alert' => 'Success', 'message' => 'Đã xóa danh mục thành công!']);
        } catch (Exception $e) {
            $this->jsonResponse(['alert' => 'Warning', 'message' => $e->getMessage()]);
        }
    }

    private function ajaxEdit(): void
    {
        $id   = (int) ($_POST['category_id'] ?? 0);
        $name = test_input($_POST['category_name'] ?? '');

        $db   = \App\Core\Database::getConnection();
        $stmt = $db->prepare('SELECT COUNT(*) FROM danh_muc_dich_vu WHERE ten_danh_muc = ? AND ma_danh_muc != ?');
        $stmt->execute([$name, $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            $this->jsonResponse(['alert' => 'Warning', 'message' => 'Tên danh mục này đã tồn tại!']);
            return;
        }

        try {
            (new ServiceCategory())->update($id, $name);
            $this->jsonResponse(['alert' => 'Success', 'message' => 'Đã cập nhật tên danh mục thành công!']);
        } catch (Exception $e) {
            $this->jsonResponse(['alert' => 'Warning', 'message' => $e->getMessage()]);
        }
    }
}

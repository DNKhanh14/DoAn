<?php

namespace App\Controllers\Admin;

use App\Models\Service;
use App\Models\ServiceCategory;
use Exception;

class ServicesController extends AdminController
{
    // ─── Routes: GET/POST services, POST ajax/services ──────────────────────

    public function index(): void
    {
        $pageTitle = 'Dịch vụ';
        $do = $_GET['do'] ?? 'Manage';

        if (!in_array($do, ['Manage', 'Add', 'Edit', 'AddCategory', 'EditCategory'], true)) {
            $do = 'Manage';
        }

        match ($do) {
            'Add'         => $this->handleAdd($pageTitle),
            'Edit'        => $this->handleEdit($pageTitle),
            'AddCategory' => $this->handleAddCategory($pageTitle),
            'EditCategory'=> $this->handleEditCategory($pageTitle),
            default       => $this->handleManage($pageTitle),
        };
    }

    /** AJAX: xóa dịch vụ — route ajax/services */
    public function ajax(): void
    {
        ob_start();
        $this->requireAuth();

        if (($_POST['do'] ?? '') === 'Delete') {
            $id = (int) ($_POST['ma_dich_vu'] ?? $_POST['service_id'] ?? 0);
            if ($id > 0) {
                (new Service())->delete($id);
            }
            $this->jsonResponse(['success' => true]);
        }

        $this->jsonResponse(['error' => 'Hành động không hợp lệ']);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function handleManage(string $pageTitle): void
    {
        $svcModel = new Service();
        $catModel = new ServiceCategory();

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            if (isset($_POST['delete_category'])) {
                try { $catModel->delete((int) ($_POST['ma_danh_muc'] ?? 0)); } catch (Exception $e) {}
            }
            if (isset($_POST['delete_service'])) {
                try { $svcModel->delete((int) ($_POST['ma_dich_vu'] ?? 0)); } catch (Exception $e) {}
            }
        }

        $q = trim($_GET['q'] ?? '');
        $this->adminView('services/manage', [
            'pageTitle'   => $pageTitle,
            'services'    => $q !== '' ? $svcModel->searchWithCategories($q) : $svcModel->getAllWithCategories(),
            'categories'  => $catModel->getAll(),
            'searchQuery' => $q,
        ], true);
    }

    private function handleAdd(string $pageTitle): void
    {
        $categories  = (new ServiceCategory())->getAll();
        $errors      = [];
        $old         = $_POST;
        $successScript = null;

        if (isset($_POST['add_new_service']) && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $errors = $this->validateService($_POST);
            if ($errors === []) {
                try {
                    (new Service())->create([
                        'ten_dich_vu' => test_input($_POST['service_name'] ?? ''),
                        'mo_ta'       => test_input($_POST['service_description'] ?? ''),
                        'gia'         => parse_vnd_input($_POST['service_price'] ?? 0),
                        'thoi_luong'  => test_input($_POST['service_duration'] ?? ''),
                        'ma_danh_muc' => (int) ($_POST['service_category'] ?? 0),
                    ]);
                    $successScript = admin_route('services');
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }

        $this->adminView('services/add', compact('pageTitle', 'categories', 'errors', 'old', 'successScript'), true);
    }

    private function handleEdit(string $pageTitle): void
    {
        $svcModel = new Service();
        $service  = $svcModel->find((int) ($_GET['ma_dich_vu'] ?? 0));
        if (!$service) { $this->redirect(admin_route('services')); }

        $categories    = (new ServiceCategory())->getAll();
        $errors        = [];
        $successScript = null;

        if (isset($_POST['edit_service_sbmt']) && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $errors = $this->validateService($_POST);
            if ($errors === []) {
                try {
                    $svcModel->update((int) $_POST['ma_dich_vu'], [
                        'ten_dich_vu' => test_input($_POST['service_name'] ?? ''),
                        'mo_ta'       => test_input($_POST['service_description'] ?? ''),
                        'gia'         => parse_vnd_input($_POST['service_price'] ?? 0),
                        'thoi_luong'  => test_input($_POST['service_duration'] ?? ''),
                        'ma_danh_muc' => (int) ($_POST['service_category'] ?? 0),
                    ]);
                    $successScript = admin_route('services');
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
            $service = array_merge($service, $_POST);
            if (isset($_POST['service_category'])) {
                $service['ma_danh_muc'] = (int) $_POST['service_category'];
            }
        }

        $this->adminView('services/edit', compact('pageTitle', 'service', 'categories', 'errors', 'successScript'), true);
    }

    private function handleAddCategory(string $pageTitle): void
    {
        $errors        = [];
        $old           = $_POST;
        $successScript = null;

        if (isset($_POST['add_new_category']) && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $name = test_input($_POST['category_name'] ?? '');
            if ($name === '') {
                $errors['category_name'] = 'Tên danh mục là bắt buộc.';
            } else {
                try {
                    (new ServiceCategory())->create($name);
                    $successScript = admin_route('services');
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }

        $this->adminView('services/add_category', compact('pageTitle', 'errors', 'old', 'successScript'), true);
    }

    private function handleEditCategory(string $pageTitle): void
    {
        $catId    = (int) ($_GET['ma_danh_muc'] ?? 0);
        $catModel = new ServiceCategory();
        $category = null;
        foreach ($catModel->getAll() as $c) {
            if ((int) $c['ma_danh_muc'] === $catId) { $category = $c; break; }
        }
        if (!$category) { $this->redirect(admin_route('services')); }

        $errors        = [];
        $successScript = null;

        if (isset($_POST['edit_category_sbmt']) && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $name = test_input($_POST['category_name'] ?? '');
            if ($name === '') {
                $errors['category_name'] = 'Tên danh mục là bắt buộc.';
            } else {
                try {
                    $catModel->update($catId, $name);
                    $successScript = admin_route('services');
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
            $category['ten_danh_muc'] = $name;
        }

        $this->adminView('services/edit_category', compact('pageTitle', 'category', 'errors', 'successScript'), true);
    }

    private function validateService(array $data): array
    {
        $errors = [];
        if (empty(test_input($data['service_name'] ?? ''))) {
            $errors['service_name'] = 'Tên dịch vụ là bắt buộc.';
        }
        $dur = test_input($data['service_duration'] ?? '');
        if ($dur === '') {
            $errors['service_duration'] = 'Thời lượng là bắt buộc.';
        } elseif (!ctype_digit($dur)) {
            $errors['service_duration'] = 'Thời lượng không hợp lệ.';
        }
        $price = trim((string) ($data['service_price'] ?? ''));
        if ($price === '' || parse_vnd_input($price) <= 0) {
            $errors['service_price'] = 'Giá không hợp lệ.';
        }
        $desc = test_input($data['service_description'] ?? '');
        if ($desc === '') {
            $errors['service_description'] = 'Mô tả là bắt buộc.';
        } elseif (strlen($desc) > 250) {
            $errors['service_description'] = 'Mô tả không quá 250 ký tự.';
        }
        return $errors;
    }
}

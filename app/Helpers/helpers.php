<?php

/**
 * Helper dùng chung toàn dự án: URL, làm sạch input, tiêu đề trang.
 */

/** In tiêu đề tab trình duyệt — trang khách (website) */
function getTitle(): void
{
    global $pageTitle;

    if (isset($pageTitle)) {
        echo $pageTitle . ' | Barbershop Website';
    } else {
        echo 'Barbershop Website';
    }
}

function getAdminTitle(): void
{
    global $pageTitle;

    if (isset($pageTitle)) {
        echo $pageTitle . ' | Barbershop ';
    } else {
        echo 'Barbershop | Barbershop ';
    }
}

/** Làm sạch dữ liệu từ form: trim, bỏ slash, chống XSS */
function test_input(string $data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}

function countItems(string $item, string $table): int
{
    $db = \App\Core\Database::getConnection();
    $stmt = $db->prepare("SELECT COUNT($item) FROM $table");
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

function checkItem(string $select, string $from, string $value): int
{
    $db = \App\Core\Database::getConnection();
    $stmt = $db->prepare("SELECT $select FROM $from WHERE $select = ?");
    $stmt->execute([$value]);

    return $stmt->rowCount();
}

function base_url(string $path = ''): string
{
    static $base;

    if ($base === null) {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = rtrim($scriptDir, '/');

        if (substr($scriptDir, -13) === '/barber-admin') {
            $scriptDir = dirname($scriptDir);
        }

        $base = $scriptDir === '' || $scriptDir === '.' ? '' : $scriptDir;
    }

    if ($path === '') {
        return ($base === '' ? '' : $base) . '/';
    }

    return ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
}

function admin_url(string $path = ''): string
{
    return base_url('barber-admin/index.php' . ($path !== '' ? '?route=' . ltrim($path, '/') : ''));
}

/**
 * Tạo URL admin: barber-admin/index.php?route=...&param=...
 * $route: tên route trong app/routes/admin.php
 */
function admin_route(string $route, array $params = []): string
{
    $query = ['route' => $route];

    foreach ($params as $key => $value) {
        $query[$key] = $value;
    }

    return base_url('barber-admin/index.php?' . http_build_query($query));
}

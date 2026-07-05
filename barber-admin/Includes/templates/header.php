<?php
// Các biến được AdminController::adminView() set vào GLOBALS trước khi require header
// Nếu không có (edge case) thì fallback từ session
if (!isset($GLOBALS['_adminRole'])) {
    $GLOBALS['_adminRole']        = $_SESSION['admin_role_barbershop'] ?? 'super_admin';
    $GLOBALS['_adminPermissions'] = $_SESSION['admin_permissions_barbershop'] ?? [];
}

$adminRole        = $GLOBALS['_adminRole'];
$adminPermissions = $GLOBALS['_adminPermissions'];
$adminName        = $adminName ?? ($_SESSION['admin_name_barbershop'] ?? ($_SESSION['username_barbershop_Xw211qAAsq4'] ?? 'Admin'));
$currentRoute     = $currentRoute ?? ($_GET['route'] ?? 'dashboard');

function admin_nav_active(string $route, string $current): string
{
    return $route === $current ? ' active' : '';
}

function admin_nav_parent_active(array $routes, string $current): string
{
    return in_array($current, $routes, true) ? ' active' : '';
}

function admin_can(string $module): bool
{
    $role        = $GLOBALS['_adminRole']        ?? 'super_admin';
    $permissions = $GLOBALS['_adminPermissions'] ?? [];
    if ($role === 'super_admin') return true;
    return (bool) ($permissions[$module] ?? false);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Hệ thống quản lý tiệm Barber">
    <title><?= htmlspecialchars($pageTitle ?? 'Quản trị') ?> | Barber</title>
    <link href="Design/fonts/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="Design/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="Design/css/main.css" rel="stylesheet">
    <link href="Design/css/barber-icons.css" rel="stylesheet">
    <link href="Design/css/admin-salon.css" rel="stylesheet">
    <?php if ($currentRoute === 'pos'): ?>
    <link href="Design/css/pos-easysalon.css" rel="stylesheet">
    <?php endif; ?>
    <?php if ($currentRoute === 'booking/create'): ?>
    <link href="Design/css/booking-create.css" rel="stylesheet">
    <?php endif; ?>
    <?php if (in_array($currentRoute, ['booking', 'booking/create', 'hr', 'hr/detail', 'settings/commission', 'employees/commission', 'reports', 'reports/order'], true)): ?>
    <link href="Design/css/admin-modern-pages.css" rel="stylesheet">
    <?php endif; ?>
    <?php if (in_array($currentRoute, ['hr', 'hr/detail'], true)): ?>
    <link href="Design/css/hr-payroll.css" rel="stylesheet">
    <?php endif; ?>
    <?php if (in_array($currentRoute, ['reports', 'reports/order'], true)): ?>
    <link href="Design/css/reports.css" rel="stylesheet">
    <?php endif; ?>
</head>
<body id="page-top" class="<?= $currentRoute === 'pos' ? 'pos-page' : '' ?><?= $currentRoute === 'booking/create' ? ' pos-page booking-create-page' : '' ?><?= $currentRoute === 'booking' ? ' booking-page' : '' ?><?= in_array($currentRoute, ['hr', 'hr/detail'], true) ? ' hr-page' : '' ?>">
<div id="wrapper">

<ul class="navbar-nav salon-sidebar sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?route=dashboard">
        <div class="salon-logo-wrap text-center">
            <i class="bs bs-scissors-1 fa-2x text-white mb-1 d-block"></i>
            <div class="sidebar-brand-text text-white" style="font-size:0.85rem;">BARBERSHOP</div>
        </div>
    </a>
    <hr class="sidebar-divider my-0">

    <!-- Bản tin — luôn hiển thị -->
    <li class="nav-item<?= admin_nav_active('dashboard', $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('dashboard', $currentRoute) ?>" href="index.php?route=dashboard">
            <i class="fas fa-fw fa-home"></i>
            <span>Bản tin</span>
        </a>
    </li>

    <!-- Thu ngân -->
    <?php if (admin_can('pos')): ?>
    <li class="nav-item<?= in_array($currentRoute, ['pos', 'pos/orders'], true) ? ' active' : '' ?>">
        <a class="nav-link<?= in_array($currentRoute, ['pos', 'pos/orders'], true) ? ' active' : '' ?>" href="index.php?route=pos">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Thu ngân</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Lịch hẹn -->
    <?php if (admin_can('booking')): ?>
    <li class="nav-item<?= admin_nav_active('booking', $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('booking', $currentRoute) ?>" href="index.php?route=booking">
            <i class="fas fa-calendar-alt"></i>
            <span>Lịch hẹn</span>
            <?php if (!empty($todayBookingCount) && $todayBookingCount > 0): ?>
                <span class="badge badge-danger badge-counter ml-1" style="font-size:0.65rem;padding:2px 5px;border-radius:10px;background:#e74a3b">
                    <?= (int) $todayBookingCount ?>
                </span>
            <?php endif; ?>
        </a>
    </li>
    <?php endif; ?>

    <!-- Khách hàng -->
    <?php if (admin_can('crm')): ?>
    <li class="nav-item<?= admin_nav_parent_active(['crm', 'clients'], $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('crm', $currentRoute) ?>" href="index.php?route=crm">
            <i class="fas fa-users"></i>
            <span>Khách hàng</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Dịch vụ -->
    <?php if (admin_can('services')): ?>
    <li class="nav-item<?= admin_nav_parent_active(['service-categories', 'services'], $currentRoute) ?>">
        <a class="nav-link<?= in_array($currentRoute, ['service-categories', 'services'], true) ? '' : ' collapsed' ?>"
           href="#" data-toggle="collapse" data-target="#menuDichVu"
           aria-expanded="<?= in_array($currentRoute, ['service-categories', 'services'], true) ? 'true' : 'false' ?>">
            <i class="fas fa-cut"></i>
            <span>Dịch vụ</span>
        </a>
        <div id="menuDichVu" class="collapse<?= in_array($currentRoute, ['service-categories', 'services'], true) ? ' show' : '' ?>" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item<?= admin_nav_active('service-categories', $currentRoute) ?>" href="index.php?route=service-categories">Danh mục dịch vụ</a>
                <a class="collapse-item<?= admin_nav_active('services', $currentRoute) ?>" href="index.php?route=services">Danh sách dịch vụ</a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Kho hàng -->
    <?php if (admin_can('inventory')): ?>
    <li class="nav-item<?= admin_nav_active('inventory', $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('inventory', $currentRoute) ?>" href="index.php?route=inventory">
            <i class="fas fa-boxes"></i>
            <span>Kho hàng</span>
        </a>
    </li>
    <?php endif; ?>

    <?php
    $employeeRoutes = ['employees', 'hr', 'hr/detail', 'employees/commission'];
    $showNhanSu = admin_can('employees') || admin_can('hr');
    ?>

    <?php if ($showNhanSu || admin_can('reports')): ?>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">NHÂN SỰ & BÁO CÁO</div>
    <?php endif; ?>

    <!-- Nhân viên -->
    <?php if ($showNhanSu): ?>
    <li class="nav-item<?= admin_nav_parent_active($employeeRoutes, $currentRoute) ?>">
        <a class="nav-link<?= in_array($currentRoute, $employeeRoutes, true) ? '' : ' collapsed' ?>"
           href="#" data-toggle="collapse" data-target="#menuNhanVien"
           aria-expanded="<?= in_array($currentRoute, $employeeRoutes, true) ? 'true' : 'false' ?>">
            <i class="fas fa-user-tie"></i>
            <span>Nhân viên</span>
        </a>
        <div id="menuNhanVien" class="collapse<?= in_array($currentRoute, $employeeRoutes, true) ? ' show' : '' ?>" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <?php if (admin_can('employees')): ?>
                <a class="collapse-item<?= admin_nav_active('employees', $currentRoute) ?>" href="index.php?route=employees">Danh sách nhân viên</a>
                <?php endif; ?>
                <?php if (admin_can('hr')): ?>
                <a class="collapse-item<?= in_array($currentRoute, ['hr', 'hr/detail'], true) ? ' active' : '' ?>" href="index.php?route=hr">Quản lý lương</a>
                <a class="collapse-item<?= admin_nav_active('employees/commission', $currentRoute) ?>" href="index.php?route=employees/commission">Hoa hồng</a>
                <?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Thống kê -->
    <?php if (admin_can('reports')): ?>
    <li class="nav-item<?= admin_nav_active('reports', $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('reports', $currentRoute) ?>" href="index.php?route=reports">
            <i class="fas fa-chart-bar"></i>
            <span>Thống kê</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Quản lý tài khoản — chỉ super_admin -->
    <?php if (admin_can('accounts')): ?>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">HỆ THỐNG</div>
    <li class="nav-item<?= admin_nav_parent_active(['accounts', 'accounts/create', 'accounts/edit', 'accounts/permissions'], $currentRoute) ?>">
        <a class="nav-link<?= in_array($currentRoute, ['accounts', 'accounts/create', 'accounts/edit', 'accounts/permissions'], true) ? '' : ' collapsed' ?>"
           href="#" data-toggle="collapse" data-target="#menuAccounts"
           aria-expanded="<?= in_array($currentRoute, ['accounts', 'accounts/create', 'accounts/edit', 'accounts/permissions'], true) ? 'true' : 'false' ?>">
            <i class="fas fa-user-shield"></i>
            <span>Tài khoản</span>
        </a>
        <div id="menuAccounts" class="collapse<?= in_array($currentRoute, ['accounts', 'accounts/create', 'accounts/edit', 'accounts/permissions'], true) ? ' show' : '' ?>" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item<?= admin_nav_active('accounts', $currentRoute) ?>" href="index.php?route=accounts">Danh sách tài khoản</a>
                <a class="collapse-item<?= admin_nav_active('accounts/permissions', $currentRoute) ?>" href="index.php?route=accounts/permissions">Phân quyền</a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<nav class="navbar navbar-expand topbar-salon mb-4 static-top">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <?php if (!empty($_SESSION['permission_denied'])): ?>
    <div class="alert alert-warning alert-dismissible fade show mx-3 py-2 mb-0" style="font-size:0.85rem;">
        <i class="fas fa-lock mr-1"></i> <?= htmlspecialchars($_SESSION['permission_denied']) ?>
        <button type="button" class="close py-2" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <?php unset($_SESSION['permission_denied']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['account_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mx-3 py-2 mb-0" style="font-size:0.85rem;">
        <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($_SESSION['account_success']) ?>
        <button type="button" class="close py-2" data-dismiss="alert"><span>&times;</span></button>
    </div>
    <?php unset($_SESSION['account_success']); ?>
    <?php endif; ?>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle p-1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Tài khoản">
                <img class="img-profile rounded-circle"
                     src="https://ui-avatars.com/api/?name=<?= urlencode($adminName) ?>&background=1e5bb8&color=fff"
                     style="width:36px;height:36px">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <div class="dropdown-item-text px-3 py-2">
                    <div class="font-weight-bold" style="font-size:0.9rem"><?= htmlspecialchars($adminName) ?></div>
                    <div class="text-muted small">
                        <?php
                        $roleLabels = [
                            'super_admin' => '<span class="badge badge-danger">Super Admin</span>',
                            'Quản lý'     => '<span class="badge badge-primary">Quản lý</span>',
                            'Lễ tân'      => '<span class="badge badge-info">Lễ tân</span>',
                            'Thợ chính'   => '<span class="badge badge-success">Thợ chính</span>',
                            'Thợ phụ'     => '<span class="badge badge-secondary">Thợ phụ</span>',
                        ];
                        echo $roleLabels[$adminRole] ?? '<span class="badge badge-secondary">' . htmlspecialchars($adminRole) . '</span>';
                        ?>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="index.php?route=dashboard"><i class="fas fa-home fa-sm fa-fw mr-2 text-gray-400"></i> Bản tin</a>
                <a class="dropdown-item" href="../" target="_blank"><i class="far fa-eye fa-sm fa-fw mr-2 text-gray-400"></i> Xem trang web</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Đăng xuất</a>
            </div>
        </li>
    </ul>
</nav>

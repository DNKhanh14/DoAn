<?php
$currentRoute = $currentRoute ?? ($_GET['route'] ?? 'dashboard');

function admin_nav_active(string $route, string $current): string
{
    return $route === $current ? ' active' : '';
}

function admin_nav_parent_active(array $routes, string $current): string
{
    return in_array($current, $routes, true) ? ' active' : '';
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
            <div class="sidebar-brand-text text-white" style="font-size:0.85rem;">Barber Salon</div>
        </div>
    </a>
    <hr class="sidebar-divider my-0">

  <!-- Bản tin -->
    <li class="nav-item<?= admin_nav_active('dashboard', $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('dashboard', $currentRoute) ?>" href="index.php?route=dashboard">
            <i class="fas fa-fw fa-home"></i>
            <span>Bản tin</span>
        </a>
    </li>

    <!-- Thu ngân -->
    <li class="nav-item<?= in_array($currentRoute, ['pos', 'pos/orders'], true) ? ' active' : '' ?>">
        <a class="nav-link<?= in_array($currentRoute, ['pos', 'pos/orders'], true) ? ' active' : '' ?>" href="index.php?route=pos">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Thu ngân</span>
        </a>
    </li>

    <!-- Lịch hẹn -->
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

    <!-- Khách hàng -->
    <li class="nav-item<?= admin_nav_parent_active(['crm', 'clients'], $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('crm', $currentRoute) ?>" href="index.php?route=crm">
            <i class="fas fa-users"></i>
            <span>Khách hàng</span>
        </a>
    </li>

    <!-- Dịch vụ -->
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

    <!-- Kho hàng -->
    <li class="nav-item<?= admin_nav_active('inventory', $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('inventory', $currentRoute) ?>" href="index.php?route=inventory">
            <i class="fas fa-boxes"></i>
            <span>Kho hàng</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">NHÂN SỰ & BÁO CÁO</div>

    <?php
    $employeeRoutes = ['employees', 'hr', 'hr/detail', 'employees/commission'];
    ?>

    <!-- Nhân viên -->
    <li class="nav-item<?= admin_nav_parent_active($employeeRoutes, $currentRoute) ?>">
        <a class="nav-link<?= in_array($currentRoute, $employeeRoutes, true) ? '' : ' collapsed' ?>"
           href="#" data-toggle="collapse" data-target="#menuNhanVien"
           aria-expanded="<?= in_array($currentRoute, $employeeRoutes, true) ? 'true' : 'false' ?>">
            <i class="fas fa-user-tie"></i>
            <span>Nhân viên</span>
        </a>
        <div id="menuNhanVien" class="collapse<?= in_array($currentRoute, $employeeRoutes, true) ? ' show' : '' ?>" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item<?= admin_nav_active('employees', $currentRoute) ?>" href="index.php?route=employees">Danh sách nhân viên</a>
                <a class="collapse-item<?= in_array($currentRoute, ['hr', 'hr/detail'], true) ? ' active' : '' ?>" href="index.php?route=hr">Quản lý lương</a>
                <a class="collapse-item<?= admin_nav_active('employees/commission', $currentRoute) ?>" href="index.php?route=employees/commission">Hoa hồng</a>
            </div>
        </div>
    </li>

    <!-- Thống kê -->
    <li class="nav-item<?= admin_nav_active('reports', $currentRoute) ?>">
        <a class="nav-link<?= admin_nav_active('reports', $currentRoute) ?>" href="index.php?route=reports">
            <i class="fas fa-chart-bar"></i>
            <span>Thống kê</span>
        </a>
    </li>

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

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle p-1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Tài khoản">
                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=Admin&background=1e5bb8&color=fff" style="width:36px;height:36px">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <div class="dropdown-item-text text-muted small px-3 py-2">
                    Đăng nhập: <strong><?= htmlspecialchars($_SESSION['username_barbershop_Xw211qAAsq4'] ?? 'admin') ?></strong>
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

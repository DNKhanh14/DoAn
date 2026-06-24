<?php

/** @var \App\Core\AdminRouter $router */

$router->get('login', 'AuthController@login');
$router->post('login', 'AuthController@login');
$router->get('logout', 'AuthController@logout');
$router->get('register', 'AuthController@register');
$router->post('register', 'AuthController@register');
$router->get('forgot-password', 'AuthController@forgotPassword');
$router->post('forgot-password', 'AuthController@forgotPassword');
$router->get('', 'DashboardController@index');
$router->get('dashboard', 'DashboardController@index');

// Lịch hẹn (Booking)
$router->get('booking', 'BookingController@index');
$router->get('booking/create', 'BookingController@create');
$router->post('booking/create', 'BookingController@create');
$router->get('booking/edit', 'BookingController@edit');
$router->post('booking/edit', 'BookingController@edit');
$router->post('booking/update-status', 'BookingController@updateStatus');
$router->post('booking/send-reminders', 'BookingController@sendReminders');

// CRM
$router->get('crm', 'CrmController@index');
$router->post('crm', 'CrmController@index');
$router->get('crm/detail', 'CrmController@detail');
$router->post('crm/detail', 'CrmController@detail');
$router->get('crm/birthdays', 'CrmController@birthdays');

// POS (Thu ngân)
$router->get('pos', 'PosController@index');
$router->get('pos/orders', 'PosController@orders');
$router->get('pos/print', 'PosController@print');
$router->post('ajax/pos', 'PosController@ajax');
$router->get('ajax/pos', 'PosController@ajax');

// Kho
$router->get('inventory', 'InventoryController@index');
$router->post('inventory', 'InventoryController@index');

// Nhân sự
$router->get('hr', 'HrController@index');
$router->post('hr', 'HrController@index');
$router->get('hr/detail', 'HrController@detail');
$router->post('ajax/hr', 'HrController@ajax');
$router->get('ajax/hr', 'HrController@ajax');

// Báo cáo
$router->get('reports', 'ReportsController@index');
$router->post('reports', 'ReportsController@index');
$router->get('reports/order', 'ReportsController@orderDetail');

// Dữ liệu cơ bản
$router->get('clients', 'CrmController@index');
$router->get('service-categories', 'ServiceCategoriesController@index');
$router->post('ajax/service-categories', 'ServiceCategoriesController@ajax');
$router->get('services', 'ServicesController@index');
$router->post('services', 'ServicesController@index');
$router->post('ajax/services', 'ServicesController@ajax');
$router->get('employees', 'EmployeesController@index');
$router->post('employees', 'EmployeesController@index');
$router->post('ajax/employees', 'EmployeesController@ajax');
$router->get('employees/commission', 'EmployeesController@commission');
$router->post('employees/commission', 'EmployeesController@commission');

$router->post('ajax/appointments', 'BookingController@ajax');

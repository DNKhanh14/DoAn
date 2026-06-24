<?php

if (defined('ROOT_PATH')) {
    return;
}

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('ADMIN_PATH', ROOT_PATH . '/barber-admin');

// Tắt display_errors để tránh PHP warning làm hỏng JSON response
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Composer autoloader (PHPMailer và các thư viện khác)
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require ROOT_PATH . '/vendor/autoload.php';
}

require APP_PATH . '/Core/Autoloader.php';

\App\Core\Autoloader::register();
require APP_PATH . '/Helpers/helpers.php';
require APP_PATH . '/Helpers/salon.php';
require APP_PATH . '/Helpers/MailService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function app_router(): \App\Core\Router
{
    static $router;

    if (!$router) {
        $router = new \App\Core\Router();
        require APP_PATH . '/routes/web.php';
    }

    return $router;
}

function admin_router(): \App\Core\AdminRouter
{
    static $router;

    if (!$router) {
        $router = new \App\Core\AdminRouter();
        require APP_PATH . '/routes/admin.php';
    }

    return $router;
}

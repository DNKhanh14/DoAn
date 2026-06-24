<?php

namespace App\Core;

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(function (string $class): void {
            $prefix = 'App\\';
            if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
                return;
            }

            $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
            $file = APP_PATH . DIRECTORY_SEPARATOR . $relative . '.php';

            if (is_file($file)) {
                require $file;
            }
        });
    }
}

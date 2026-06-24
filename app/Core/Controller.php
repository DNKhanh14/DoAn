<?php

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = APP_PATH . '/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view);
            return;
        }

        if ($layout === null) {
            require $viewFile;
            return;
        }

        $layoutFile = APP_PATH . '/Views/layouts/' . $layout . '.php';

        if (!is_file($layoutFile)) {
            require $viewFile;
            return;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }
}

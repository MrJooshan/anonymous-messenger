<?php
namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require __DIR__ . '/../../views/layouts/app.php';
    }
}
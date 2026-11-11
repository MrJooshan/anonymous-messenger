<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->add('GET', $path, $handler);
    }
    public function post(string $path, array $handler): void {
        $this->add('POST', $path, $handler);
    }
    private function add(string $method, string $path, array $handler): void {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $r) {
            $pattern = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $r['path']) . '$#';
            if ($r['method'] === $method && preg_match($pattern, $path, $m)) {
                [$class, $action] = $r['handler'];
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                (new $class())->$action(...array_values($params));
                return;
            }
        }
        http_response_code(404);
        echo '404 Not Found';
    }
}
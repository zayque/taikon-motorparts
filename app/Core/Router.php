<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Tiny router: exact paths plus `{param}` segments, per-route middleware.
 *
 *   $router->get('/shop/{slug}', [ShopController::class, 'show']);
 *   $router->post('/admin/products', [...], ['admin']);
 *
 * Every POST request is CSRF-checked automatically.
 */
final class Router
{
    /** @var array<string, list<array{pattern: string, handler: array{0: class-string, 1: string}, middleware: list<string>}>> */
    private array $routes = [];

    /** @var list<string> */
    private array $groupMiddleware = [];
    private string $groupPrefix = '';

    /** @param list<string> $middleware */
    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    /** @param list<string> $middleware */
    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    /** @param list<string> $middleware */
    public function group(string $prefix, array $middleware, callable $routes): void
    {
        [$prevPrefix, $prevMiddleware] = [$this->groupPrefix, $this->groupMiddleware];
        $this->groupPrefix = $prevPrefix . $prefix;
        $this->groupMiddleware = array_merge($prevMiddleware, $middleware);

        $routes($this);

        [$this->groupPrefix, $this->groupMiddleware] = [$prevPrefix, $prevMiddleware];
    }

    /** @param list<string> $middleware */
    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $path = '/' . trim($this->groupPrefix . $path, '/');
        $regex = preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[^/]+)', $path);

        $this->routes[$method][] = [
            'pattern'    => '#^' . $regex . '$#',
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes[$method] ?? [] as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            if ($method === 'POST' && !Csrf::verify($_POST['_token'] ?? null)) {
                throw new HttpException(419, 'Your session expired. Please refresh the page and try again.');
            }

            foreach ($route['middleware'] as $middleware) {
                Middleware::handle($middleware);
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            [$class, $action] = $route['handler'];

            (new $class())->$action(...array_values($params));

            return;
        }

        throw HttpException::notFound();
    }
}

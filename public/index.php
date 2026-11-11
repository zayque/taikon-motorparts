<?php

declare(strict_types=1);

/*
 * Front controller — every request that isn't a static file lands here.
 */

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Router;
use App\Core\View;

// Let `php -S` serve real static files directly.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file) && !str_ends_with($file, '.php')) {
        return false;
    }
}

require dirname(__DIR__) . '/app/bootstrap.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

$router = new Router();
require BASE_PATH . '/routes/web.php';

$layout = str_starts_with(Request::path(), '/admin') ? 'admin' : 'main';

try {
    $router->dispatch(Request::method(), Request::path());
} catch (HttpException $e) {
    http_response_code($e->status);
    echo View::render('errors/http', [
        'title'   => $e->status === 404 ? 'Page not found' : 'Something went wrong',
        'status'  => $e->status,
        'message' => $e->getMessage(),
    ], $layout === 'admin' && $e->status !== 403 ? 'admin' : 'main');
} catch (Throwable $e) {
    error_log((string) $e);
    http_response_code(500);

    if (config('app.debug')) {
        echo '<pre style="padding:2rem;white-space:pre-wrap">' . e((string) $e) . '</pre>';
    } else {
        echo View::render('errors/http', [
            'title'   => 'Server error',
            'status'  => 500,
            'message' => 'Something went wrong on our side. Please try again in a moment.',
        ], 'minimal');
    }
}

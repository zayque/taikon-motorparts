<?php

declare(strict_types=1);

use App\Controllers\AccountController;
use App\Controllers\Admin;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\ServiceController;
use App\Controllers\ShopController;
use App\Core\Router;

/** @var Router $router */

// ---- Storefront ---------------------------------------------------------
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/contact', [PageController::class, 'contact']);
$router->post('/contact', [PageController::class, 'sendMessage']);

$router->get('/shop', [ShopController::class, 'index']);
$router->get('/shop/category/{slug}', [ShopController::class, 'category']);
$router->get('/shop/{slug}', [ShopController::class, 'show']);

$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);

$router->get('/services', [ServiceController::class, 'index']);
$router->get('/services/book', [ServiceController::class, 'book']);
$router->get('/services/slots', [ServiceController::class, 'slots']);
$router->post('/services/book', [ServiceController::class, 'store']);
$router->get('/services/book/confirmed/{number}', [ServiceController::class, 'confirmed']);

// ---- Authentication -----------------------------------------------------
$router->group('', ['guest'], static function (Router $r): void {
    $r->get('/login', [AuthController::class, 'showLogin']);
    $r->post('/login', [AuthController::class, 'login']);
    $r->get('/register', [AuthController::class, 'showRegister']);
    $r->post('/register', [AuthController::class, 'register']);
});
$router->post('/logout', [AuthController::class, 'logout']);

// ---- Customer area ------------------------------------------------------
$router->group('', ['auth'], static function (Router $r): void {
    $r->get('/checkout', [CheckoutController::class, 'index']);
    $r->post('/checkout', [CheckoutController::class, 'store']);
    $r->get('/checkout/complete/{number}', [CheckoutController::class, 'complete']);

    $r->get('/account', [AccountController::class, 'index']);
    $r->get('/account/orders', [AccountController::class, 'orders']);
    $r->get('/account/orders/{number}', [AccountController::class, 'order']);
    $r->get('/account/bookings', [AccountController::class, 'bookings']);
    $r->post('/account/bookings/{id}/cancel', [AccountController::class, 'cancelBooking']);
    $r->get('/account/profile', [AccountController::class, 'profile']);
    $r->post('/account/profile', [AccountController::class, 'updateProfile']);
    $r->post('/account/password', [AccountController::class, 'updatePassword']);
});

// ---- Admin --------------------------------------------------------------
$router->group('/admin', ['admin'], static function (Router $r): void {
    $r->get('', [Admin\DashboardController::class, 'index']);

    $r->get('/products', [Admin\ProductController::class, 'index']);
    $r->get('/products/create', [Admin\ProductController::class, 'create']);
    $r->post('/products', [Admin\ProductController::class, 'store']);
    $r->get('/products/{id}/edit', [Admin\ProductController::class, 'edit']);
    $r->post('/products/{id}', [Admin\ProductController::class, 'update']);
    $r->post('/products/{id}/delete', [Admin\ProductController::class, 'destroy']);

    $r->get('/categories', [Admin\CategoryController::class, 'index']);
    $r->post('/categories', [Admin\CategoryController::class, 'store']);
    $r->post('/categories/{id}', [Admin\CategoryController::class, 'update']);
    $r->post('/categories/{id}/delete', [Admin\CategoryController::class, 'destroy']);

    $r->get('/orders', [Admin\OrderController::class, 'index']);
    $r->get('/orders/{id}', [Admin\OrderController::class, 'show']);
    $r->post('/orders/{id}/status', [Admin\OrderController::class, 'updateStatus']);

    $r->get('/bookings', [Admin\BookingController::class, 'index']);
    $r->get('/bookings/{id}', [Admin\BookingController::class, 'show']);
    $r->post('/bookings/{id}/status', [Admin\BookingController::class, 'updateStatus']);

    $r->get('/services', [Admin\ServiceController::class, 'index']);
    $r->get('/services/create', [Admin\ServiceController::class, 'create']);
    $r->post('/services', [Admin\ServiceController::class, 'store']);
    $r->get('/services/{id}/edit', [Admin\ServiceController::class, 'edit']);
    $r->post('/services/{id}', [Admin\ServiceController::class, 'update']);
    $r->post('/services/{id}/delete', [Admin\ServiceController::class, 'destroy']);

    $r->get('/customers', [Admin\CustomerController::class, 'index']);

    $r->get('/messages', [Admin\MessageController::class, 'index']);
    $r->get('/messages/{id}', [Admin\MessageController::class, 'show']);
    $r->post('/messages/{id}/delete', [Admin\MessageController::class, 'destroy']);
});

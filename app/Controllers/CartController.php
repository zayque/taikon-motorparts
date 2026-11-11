<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\Product;
use App\Services\Cart;

final class CartController extends Controller
{
    public function index(): void
    {
        $summary = Cart::summary();

        if ($summary['adjusted']) {
            flash('warning', 'Some quantities were adjusted to match available stock.');
        }

        $this->view('cart/index', [
            'title'    => 'Your cart',
            'cart'     => $summary,
            'shipping' => Cart::shippingFor($summary['subtotal'], 'delivery'),
        ]);
    }

    public function add(): void
    {
        $product = Product::find((int) Request::input('product_id'));

        if ($product === null || !$product['is_active']) {
            flash('error', 'That product is no longer available.');
            back('/shop');
        }

        if ((int) $product['stock'] <= 0) {
            flash('error', $product['name'] . ' is out of stock.');
            back('/shop');
        }

        $requested = max(1, (int) Request::input('quantity', 1));
        $inCart = Cart::add($product, $requested);

        if ($this->wantsJson()) {
            $this->json(['ok' => true, 'count' => Cart::count(), 'quantity' => $inCart, 'name' => $product['name']]);
            return;
        }

        flash('success', $product['name'] . ' added to your cart.');
        Request::input('buy_now') ? redirect('/cart') : back('/shop');
    }

    public function update(): void
    {
        $quantities = $_POST['quantities'] ?? [];

        if (is_array($quantities)) {
            foreach ($quantities as $productId => $qty) {
                Cart::set((int) $productId, (int) $qty);
            }
        }

        flash('success', 'Cart updated.');
        redirect('/cart');
    }

    public function remove(): void
    {
        Cart::remove((int) Request::input('product_id'));
        flash('success', 'Item removed from your cart.');
        redirect('/cart');
    }

    private function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}

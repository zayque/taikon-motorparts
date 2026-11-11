<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Request;
use App\Models\Order;
use App\Services\Cart;
use App\Services\OrderPlacer;
use RuntimeException;

final class CheckoutController extends Controller
{
    public function index(): void
    {
        $summary = Cart::summary();

        if ($summary['lines'] === []) {
            flash('info', 'Your cart is empty.');
            redirect('/shop');
        }

        $this->view('checkout/index', [
            'title' => 'Checkout',
            'cart'  => $summary,
            'user'  => Auth::user(),
        ]);
    }

    public function store(): void
    {
        $data = Request::only([
            'name', 'email', 'phone', 'fulfilment', 'address_line', 'city',
            'postcode', 'state', 'payment_method', 'notes',
        ]);
        $isDelivery = $data['fulfilment'] === 'delivery';

        $rules = [
            'name'           => 'required|max:100',
            'email'          => 'required|email|max:150',
            'phone'          => 'required|phone',
            'fulfilment'     => 'required|in:' . implode(',', array_keys(config('shipping.methods'))),
            'payment_method' => 'required|in:' . implode(',', array_keys(config('shipping.payment_methods'))),
            'notes'          => 'max:500',
        ];

        if ($isDelivery) {
            $rules += [
                'address_line' => 'required|max:255',
                'city'         => 'required|max:80',
                'postcode'     => 'required|regex:/^\d{5}$/',
                'state'        => 'required|max:80',
            ];
        }

        $this->validate($data, $rules, ['address_line' => 'Address', 'payment_method' => 'payment method'], static function ($v) use ($data, $isDelivery) {
            if ($isDelivery && $data['payment_method'] === 'pay_at_store') {
                $v->addError('payment_method', 'Pay at workshop is only available for self-pickup.');
            }
        });

        try {
            $order = OrderPlacer::place(Auth::id(), $data);
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('/cart');
        }

        redirect('/checkout/complete/' . $order['order_number']);
    }

    public function complete(string $number): void
    {
        // Scoped to the signed-in owner, so order numbers can't be enumerated.
        $order = Order::findForUser($number, (int) Auth::id()) ?? throw HttpException::notFound('Order not found');

        $this->view('checkout/complete', [
            'title' => 'Order placed',
            'order' => $order,
            'items' => Order::items((int) $order['id']),
        ]);
    }
}

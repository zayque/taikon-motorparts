<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Request;
use App\Models\Booking;
use App\Models\Order;
use App\Models\User;

final class AccountController extends Controller
{
    public function index(): void
    {
        $userId = (int) Auth::id();
        $orders = Order::forUser($userId);
        $bookings = Booking::forUser($userId);
        $today = date('Y-m-d');

        $this->view('account/index', [
            'title'         => 'My account',
            'orders'        => array_slice($orders, 0, 5),
            'bookings'      => array_slice($bookings, 0, 5),
            'orderCount'    => count($orders),
            'upcomingCount' => count(array_filter(
                $bookings,
                static fn ($b) => $b['booking_date'] >= $today && in_array($b['status'], ['pending', 'confirmed'], true)
            )),
        ]);
    }

    public function orders(): void
    {
        $this->view('account/orders', [
            'title'  => 'My orders',
            'orders' => Order::forUser((int) Auth::id()),
        ]);
    }

    public function order(string $number): void
    {
        $order = Order::findForUser($number, (int) Auth::id()) ?? throw HttpException::notFound('Order not found');

        $this->view('account/order', [
            'title' => 'Order ' . $order['order_number'],
            'order' => $order,
            'items' => Order::items((int) $order['id']),
        ]);
    }

    public function bookings(): void
    {
        $this->view('account/bookings', [
            'title'    => 'My bookings',
            'bookings' => Booking::forUser((int) Auth::id()),
        ]);
    }

    public function cancelBooking(string $id): void
    {
        if (Booking::cancelForUser((int) $id, (int) Auth::id())) {
            flash('success', 'Your booking has been cancelled.');
        } else {
            flash('error', 'This booking can no longer be cancelled online. Please call us.');
        }

        redirect('/account/bookings');
    }

    public function profile(): void
    {
        $this->view('account/profile', ['title' => 'Profile & address', 'user' => Auth::user()]);
    }

    public function updateProfile(): void
    {
        $userId = (int) Auth::id();
        $data = Request::only(['name', 'email', 'phone', 'address_line', 'city', 'postcode', 'state']);

        $this->validate(
            $data,
            [
                'name'         => 'required|max:100',
                'email'        => 'required|email|max:150',
                'phone'        => 'phone',
                'address_line' => 'max:255',
                'city'         => 'max:80',
                'postcode'     => 'regex:/^\d{5}$/',
                'state'        => 'max:80',
            ],
            ['address_line' => 'Address'],
            static function ($v) use ($data, $userId) {
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) && User::emailTaken($data['email'], $userId)) {
                    $v->addError('email', 'Another account already uses this email.');
                }
            }
        );

        User::updateProfile($userId, $data);
        flash('success', 'Profile updated.');
        redirect('/account/profile');
    }

    public function updatePassword(): void
    {
        $user = Auth::user();
        $data = Request::only(['current_password', 'password', 'password_confirmation']);

        $this->validate(
            $data,
            ['current_password' => 'required', 'password' => 'required|min:8|max:72|confirmed'],
            ['password' => 'New password'],
            static function ($v) use ($data, $user) {
                if (!password_verify((string) $data['current_password'], $user['password_hash'])) {
                    $v->addError('current_password', 'Your current password is incorrect.');
                }
            },
            '/account/profile'
        );

        User::updatePassword((int) $user['id'], $data['password']);
        flash('success', 'Password changed.');
        redirect('/account/profile');
    }
}

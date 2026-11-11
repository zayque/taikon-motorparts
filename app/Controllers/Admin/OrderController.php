<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\HttpException;
use App\Core\Request;
use App\Models\Order;

final class OrderController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q'      => (string) Request::query('q', ''),
            'status' => (string) Request::query('status', ''),
        ];

        $this->view('admin/orders/index', [
            'title'   => 'Orders',
            'filters' => $filters,
            'counts'  => Order::countsByStatus(),
            'orders'  => Order::paginateForAdmin($filters, (int) config('catalog.per_page.admin')),
        ], 'admin');
    }

    public function show(string $id): void
    {
        $order = Order::find((int) $id) ?? throw HttpException::notFound();

        $this->view('admin/orders/show', [
            'title' => 'Order ' . $order['order_number'],
            'order' => $order,
            'items' => Order::items((int) $order['id']),
        ], 'admin');
    }

    public function updateStatus(string $id): void
    {
        $order = Order::find((int) $id) ?? throw HttpException::notFound();
        $status = (string) Request::input('status');

        if (!in_array($status, Order::STATUSES, true)) {
            flash('error', 'Unknown status.');
            redirect('/admin/orders/' . $order['id']);
        }

        Order::updateStatus((int) $order['id'], $status);

        $note = $status === 'cancelled' ? ' Stock has been returned to inventory.' : '';
        flash('success', 'Order marked as ' . $status . '.' . $note);
        redirect('/admin/orders/' . $order['id']);
    }
}

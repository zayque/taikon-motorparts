<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin/dashboard', [
            'title'          => 'Dashboard',
            'revenueMonth'   => Order::revenueSince(date('Y-m-01')),
            'revenueToday'   => Order::revenueSince(date('Y-m-d')),
            'orderCounts'    => Order::countsByStatus(),
            'pendingBookings'=> Booking::countPending(),
            'unreadMessages' => Message::countUnread(),
            'customers'      => User::countCustomers(),
            'products'       => Product::count(),
            'daily'          => Order::dailyRevenue(14),
            'recentOrders'   => Order::recent(6),
            'upcoming'       => Booking::upcoming(6),
            'lowStock'       => Product::lowStock(6),
        ], 'admin');
    }
}

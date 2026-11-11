<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\HttpException;
use App\Core\Request;
use App\Models\Booking;

final class BookingController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q'      => (string) Request::query('q', ''),
            'status' => (string) Request::query('status', ''),
            'when'   => (string) Request::query('when', 'upcoming'),
        ];

        $this->view('admin/bookings/index', [
            'title'    => 'Bookings',
            'filters'  => $filters,
            'bookings' => Booking::paginateForAdmin($filters, (int) config('catalog.per_page.admin')),
        ], 'admin');
    }

    public function show(string $id): void
    {
        $booking = Booking::find((int) $id) ?? throw HttpException::notFound();

        $this->view('admin/bookings/show', ['title' => 'Booking ' . $booking['booking_number'], 'booking' => $booking], 'admin');
    }

    public function updateStatus(string $id): void
    {
        $booking = Booking::find((int) $id) ?? throw HttpException::notFound();
        $status = (string) Request::input('status');

        if (in_array($status, Booking::STATUSES, true)) {
            Booking::updateStatus((int) $booking['id'], $status);
            flash('success', 'Booking marked as ' . $status . '.');
        }

        back('/admin/bookings/' . $booking['id']);
    }
}

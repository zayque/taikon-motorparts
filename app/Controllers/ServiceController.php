<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Service;
use App\Support\BookingCalendar;

final class ServiceController extends Controller
{
    public function index(): void
    {
        $this->view('services/index', [
            'title'       => 'Workshop services',
            'description' => 'Servicing, repairs, accessory fitting and custom build packages at our Kuang workshop.',
            'services'    => Service::active(),
        ]);
    }

    public function book(): void
    {
        $date = (string) old('booking_date', '');
        if (!BookingCalendar::isBookable($date)) {
            $date = BookingCalendar::minDate();
        }

        $this->view('services/book', [
            'title'    => 'Book a service',
            'services' => Service::active(),
            'selected' => (int) old('service_id', Request::query('service', 0)),
            'user'     => Auth::user(),
            'date'     => $date,
            'slots'    => Booking::availability($date, BookingCalendar::slotsFor($date), (int) config('booking.slot_capacity')),
            'minDate'  => BookingCalendar::minDate(),
            'maxDate'  => BookingCalendar::maxDate(),
        ]);
    }

    /** JSON: remaining capacity per slot for ?date=YYYY-MM-DD */
    public function slots(): void
    {
        $date = (string) Request::query('date', '');

        if (!BookingCalendar::isBookable($date)) {
            $this->json(['date' => $date, 'slots' => []]);
            return;
        }

        $availability = Booking::availability($date, BookingCalendar::slotsFor($date), (int) config('booking.slot_capacity'));
        $slots = [];
        foreach ($availability as $time => $left) {
            $slots[] = ['time' => $time, 'label' => date('g:i A', strtotime($time)), 'left' => $left];
        }

        $this->json(['date' => $date, 'slots' => $slots]);
    }

    public function store(): void
    {
        $data = Request::only([
            'service_id', 'customer_name', 'email', 'phone', 'motorcycle', 'plate_number',
            'booking_date', 'booking_time', 'notes',
        ]);
        $service = Service::findActive((int) $data['service_id']);

        $this->validate(
            $data,
            [
                'service_id'    => 'required',
                'customer_name' => 'required|max:100',
                'email'         => 'required|email|max:150',
                'phone'         => 'required|phone',
                'motorcycle'    => 'required|max:120',
                'plate_number'  => 'max:15',
                'booking_date'  => 'required|date',
                'booking_time'  => 'required',
                'notes'         => 'max:1000',
            ],
            ['customer_name' => 'Name', 'motorcycle' => 'Motorcycle make & model', 'booking_date' => 'Date', 'booking_time' => 'Time'],
            static function ($v) use ($data, $service) {
                if ($service === null) {
                    $v->addError('service_id', 'Choose a service.');
                }
                if (!BookingCalendar::isBookable($data['booking_date'])) {
                    $v->addError('booking_date', 'Choose a date between tomorrow and ' . format_date(BookingCalendar::maxDate()) . '.');
                } elseif (!in_array($data['booking_time'], BookingCalendar::slotsFor($data['booking_date']), true)) {
                    $v->addError('booking_time', 'Choose an available time slot.');
                }
            }
        );

        $id = Booking::createIfAvailable([
            'user_id'       => Auth::id(),
            'service_id'    => (int) $service['id'],
            'service_name'  => $service['name'],
            'customer_name' => $data['customer_name'],
            'email'         => strtolower($data['email']),
            'phone'         => $data['phone'],
            'motorcycle'    => $data['motorcycle'],
            'plate_number'  => $data['plate_number'] !== '' ? strtoupper($data['plate_number']) : null,
            'booking_date'  => $data['booking_date'],
            'booking_time'  => $data['booking_time'] . ':00',
            'notes'         => $data['notes'] ?: null,
        ], (int) config('booking.slot_capacity'));

        if ($id === null) {
            Session::flashInput($data);
            Session::flashErrors(['booking_time' => 'That slot was just taken. Please pick another time.']);
            redirect('/services/book');
        }

        $booking = Booking::find($id);
        Session::put('last_booking', $booking['booking_number']);
        redirect('/services/book/confirmed/' . $booking['booking_number']);
    }

    public function confirmed(string $number): void
    {
        $booking = Booking::findByNumber($number);
        $ownsIt = $booking !== null && (
            Session::get('last_booking') === $number
            || ($booking['user_id'] !== null && (int) $booking['user_id'] === Auth::id())
        );

        if (!$ownsIt) {
            throw HttpException::notFound('Booking not found');
        }

        $this->view('services/confirmed', ['title' => 'Booking received', 'booking' => $booking]);
    }
}

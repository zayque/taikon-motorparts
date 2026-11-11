<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\User;

final class CustomerController extends Controller
{
    public function index(): void
    {
        $q = (string) Request::query('q', '');

        $this->view('admin/customers/index', [
            'title'     => 'Customers',
            'q'         => $q,
            'customers' => User::paginateCustomers($q, (int) config('catalog.per_page.admin')),
        ], 'admin');
    }
}

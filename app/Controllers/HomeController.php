<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;

final class HomeController extends Controller
{
    public function index(): void
    {
        $services = Service::active();

        $this->view('pages/home', [
            'title'        => null,
            'categories'   => Category::all(),
            'featured'     => Product::featured(8),
            'services'     => array_slice($services, 0, 4),
            'serviceCount' => count($services),
        ]);
    }
}

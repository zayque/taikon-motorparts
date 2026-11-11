<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;
use App\Models\Category;
use App\Models\Product;

final class ShopController extends Controller
{
    public function index(): void
    {
        $this->listing(null);
    }

    public function category(string $slug): void
    {
        $category = Category::findBySlug($slug) ?? throw HttpException::notFound('Category not found');
        $this->listing($category);
    }

    public function show(string $slug): void
    {
        $product = Product::findActiveBySlug($slug) ?? throw HttpException::notFound('Product not found');

        $this->view('shop/show', [
            'title'       => $product['name'],
            'description' => str_excerpt($product['description'], 155),
            'product'     => $product,
            'related'     => Product::related($product),
        ]);
    }

    /** @param array<string, mixed>|null $category */
    private function listing(?array $category): void
    {
        $sort = (string) Request::query('sort', 'newest');
        $filters = [
            'category_id' => $category['id'] ?? null,
            'q'           => mb_substr((string) Request::query('q', ''), 0, 80),
            'sort'        => array_key_exists($sort, Product::SORTS) ? $sort : 'newest',
            'in_stock'    => Request::query('in_stock') === '1',
        ];

        $this->view('shop/index', [
            'title'       => $category['name'] ?? ($filters['q'] !== '' ? 'Search results' : 'Shop all parts'),
            'description' => $category['description'] ?? 'Browse spare parts, accessories, riding gear and tools.',
            'category'    => $category,
            'categories'  => Category::all(),
            'filters'     => $filters,
            'products'    => Product::paginateForShop($filters, (int) config('catalog.per_page.shop')),
        ]);
    }
}

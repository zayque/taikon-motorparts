<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Database;
use App\Core\HttpException;
use App\Core\Request;
use App\Core\Session;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploader;
use RuntimeException;

final class ProductController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q'           => (string) Request::query('q', ''),
            'category_id' => (int) Request::query('category', 0) ?: null,
            'stock'       => (string) Request::query('stock', ''),
        ];

        $this->view('admin/products/index', [
            'title'      => 'Products',
            'filters'    => $filters,
            'categories' => Category::all(),
            'products'   => Product::paginateForAdmin($filters, (int) config('catalog.per_page.admin')),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin/products/form', [
            'title'      => 'Add product',
            'product'    => null,
            'categories' => Category::all(),
        ], 'admin');
    }

    public function store(): void
    {
        $data = $this->validated(null);

        try {
            $data['image'] = ($file = Request::file('image')) ? ImageUploader::store($file) : null;
        } catch (RuntimeException $e) {
            $this->failUpload($e, null);
        }

        $id = Product::create($data);
        flash('success', 'Product created.');
        redirect('/admin/products/' . $id . '/edit');
    }

    public function edit(string $id): void
    {
        $product = Product::find((int) $id) ?? throw HttpException::notFound();

        $this->view('admin/products/form', [
            'title'      => 'Edit ' . $product['name'],
            'product'    => $product,
            'categories' => Category::all(),
        ], 'admin');
    }

    public function update(string $id): void
    {
        $product = Product::find((int) $id) ?? throw HttpException::notFound();
        $data = $this->validated((int) $product['id']);

        try {
            if ($file = Request::file('image')) {
                $data['image'] = ImageUploader::store($file);
                ImageUploader::delete($product['image']);
            } elseif (Request::input('remove_image') === '1') {
                $data['image'] = null;
                ImageUploader::delete($product['image']);
            }
        } catch (RuntimeException $e) {
            $this->failUpload($e, (int) $product['id']);
        }

        Product::update((int) $product['id'], $data);
        flash('success', 'Product saved.');
        redirect('/admin/products/' . $product['id'] . '/edit');
    }

    public function destroy(string $id): void
    {
        $product = Product::find((int) $id) ?? throw HttpException::notFound();

        $hasOrders = (bool) Database::value('SELECT 1 FROM order_items WHERE product_id = ? LIMIT 1', [$product['id']]);

        if ($hasOrders) {
            // Keep order history intact: hide instead of deleting.
            Product::update((int) $product['id'], ['is_active' => 0]);
            flash('info', 'This product has past orders, so it was hidden from the shop instead of deleted.');
        } else {
            Product::delete((int) $product['id']);
            ImageUploader::delete($product['image']);
            flash('success', 'Product deleted.');
        }

        redirect('/admin/products');
    }

    /** @return array<string, mixed> */
    private function validated(?int $id): array
    {
        $data = Request::only([
            'name', 'slug', 'sku', 'category_id', 'brand', 'fits', 'description',
            'price', 'compare_price', 'stock', 'is_active', 'is_featured',
        ]);
        $data['slug'] = slugify($data['slug'] !== '' ? $data['slug'] : $data['name']);
        $data['sku'] = strtoupper($data['sku']);

        $this->validate(
            $data,
            [
                'name'          => 'required|max:150',
                'sku'           => 'required|max:40|regex:/^[A-Z0-9\-]+$/',
                'category_id'   => 'required|integer',
                'brand'         => 'max:60',
                'fits'          => 'max:255',
                'price'         => 'required|numeric|gte:0',
                'compare_price' => 'numeric|gte:0',
                'stock'         => 'required|integer|gte:0',
            ],
            ['sku' => 'SKU', 'category_id' => 'category', 'compare_price' => 'Compare-at price'],
            static function ($v) use ($data, $id) {
                if (Product::slugTaken($data['slug'], $id)) {
                    $v->addError('slug', 'Another product already uses this URL slug.');
                }
                if (Product::skuTaken($data['sku'], $id)) {
                    $v->addError('sku', 'Another product already uses this SKU.');
                }
                if (Category::find((int) $data['category_id']) === null) {
                    $v->addError('category_id', 'Choose a category.');
                }
                if ($data['compare_price'] !== '' && (float) $data['compare_price'] <= (float) $data['price']) {
                    $v->addError('compare_price', 'Compare-at price must be higher than the selling price.');
                }
            },
            self::formPath($id)
        );

        return [
            'name'          => $data['name'],
            'slug'          => $data['slug'],
            'sku'           => $data['sku'],
            'category_id'   => (int) $data['category_id'],
            'brand'         => $data['brand'] ?: null,
            'fits'          => $data['fits'] ?: null,
            'description'   => $data['description'] ?: null,
            'price'         => round((float) $data['price'], 2),
            'compare_price' => $data['compare_price'] !== '' ? round((float) $data['compare_price'], 2) : null,
            'stock'         => (int) $data['stock'],
            'is_featured'   => $data['is_featured'] === '1' ? 1 : 0,
            'is_active'     => $data['is_active'] === '1' ? 1 : 0,
        ];
    }

    private static function formPath(?int $id): string
    {
        return $id ? '/admin/products/' . $id . '/edit' : '/admin/products/create';
    }

    private function failUpload(RuntimeException $e, ?int $id): never
    {
        Session::flashInput($_POST);
        Session::flashErrors(['image' => $e->getMessage()]);
        flash('error', 'Please check the highlighted fields.');
        back(self::formPath($id));
    }
}

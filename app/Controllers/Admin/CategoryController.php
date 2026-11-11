<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\HttpException;
use App\Core\Request;
use App\Models\Category;

final class CategoryController extends Controller
{
    public const ICONS = ['cog', 'sparkles', 'helmet', 'hammer', 'wrench', 'box', 'shield', 'gauge', 'bike', 'tag', 'package'];

    public function index(): void
    {
        $editId = (int) Request::query('edit', 0);

        $this->view('admin/categories/index', [
            'title'      => 'Categories',
            'categories' => Category::all(),
            'editing'    => $editId ? Category::find($editId) : null,
            'icons'      => self::ICONS,
        ], 'admin');
    }

    public function store(): void
    {
        Category::create($this->validated(null));
        flash('success', 'Category added.');
        redirect('/admin/categories');
    }

    public function update(string $id): void
    {
        $category = Category::find((int) $id) ?? throw HttpException::notFound();
        Category::update((int) $category['id'], $this->validated((int) $category['id']));
        flash('success', 'Category saved.');
        redirect('/admin/categories');
    }

    public function destroy(string $id): void
    {
        $category = Category::find((int) $id) ?? throw HttpException::notFound();

        if (Category::hasProducts((int) $category['id'])) {
            flash('error', 'Move or delete the products in "' . $category['name'] . '" before deleting it.');
        } else {
            Category::delete((int) $category['id']);
            flash('success', 'Category deleted.');
        }

        redirect('/admin/categories');
    }

    /** @return array<string, mixed> */
    private function validated(?int $id): array
    {
        $data = Request::only(['name', 'slug', 'description', 'icon', 'sort_order']);
        $data['slug'] = slugify($data['slug'] !== '' ? $data['slug'] : $data['name']);

        $this->validate(
            $data,
            [
                'name'        => 'required|max:80',
                'description' => 'max:255',
                'icon'        => 'in:' . implode(',', self::ICONS),
                'sort_order'  => 'integer',
            ],
            [],
            static function ($v) use ($data, $id) {
                if (Category::slugTaken($data['slug'], $id)) {
                    $v->addError('slug', 'Another category already uses this slug.');
                }
            },
            $id ? '/admin/categories?edit=' . $id : '/admin/categories'
        );

        return $data;
    }
}

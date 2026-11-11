<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\HttpException;
use App\Core\Request;
use App\Models\Service;

final class ServiceController extends Controller
{
    public const ICONS = ['wrench', 'gauge', 'shield', 'cog', 'sparkles', 'hammer', 'bike', 'helmet'];

    public function index(): void
    {
        $this->view('admin/services/index', ['title' => 'Services', 'services' => Service::all()], 'admin');
    }

    public function create(): void
    {
        $this->view('admin/services/form', ['title' => 'Add service', 'service' => null, 'icons' => self::ICONS], 'admin');
    }

    public function store(): void
    {
        $id = Service::create($this->validated(null));
        flash('success', 'Service created.');
        redirect('/admin/services/' . $id . '/edit');
    }

    public function edit(string $id): void
    {
        $service = Service::find((int) $id) ?? throw HttpException::notFound();
        $this->view('admin/services/form', ['title' => 'Edit ' . $service['name'], 'service' => $service, 'icons' => self::ICONS], 'admin');
    }

    public function update(string $id): void
    {
        $service = Service::find((int) $id) ?? throw HttpException::notFound();
        Service::update((int) $service['id'], $this->validated((int) $service['id']));
        flash('success', 'Service saved.');
        redirect('/admin/services/' . $service['id'] . '/edit');
    }

    public function destroy(string $id): void
    {
        $service = Service::find((int) $id) ?? throw HttpException::notFound();
        // Bookings keep a copy of the service name, so deleting is safe.
        Service::delete((int) $service['id']);
        flash('success', 'Service deleted.');
        redirect('/admin/services');
    }

    /** @return array<string, mixed> */
    private function validated(?int $id): array
    {
        $data = Request::only(['name', 'slug', 'summary', 'description', 'features', 'price_from', 'duration_minutes', 'icon', 'sort_order', 'is_package', 'is_active']);
        $data['slug'] = slugify($data['slug'] !== '' ? $data['slug'] : $data['name']);

        $this->validate(
            $data,
            [
                'name'             => 'required|max:120',
                'summary'          => 'required|max:255',
                'price_from'       => 'required|numeric|gte:0',
                'duration_minutes' => 'required|integer|gte:15',
                'icon'             => 'in:' . implode(',', self::ICONS),
                'sort_order'       => 'integer',
            ],
            ['price_from' => 'Starting price', 'duration_minutes' => 'Duration'],
            static function ($v) use ($data, $id) {
                if (Service::slugTaken($data['slug'], $id)) {
                    $v->addError('slug', 'Another service already uses this slug.');
                }
            },
            $id ? '/admin/services/' . $id . '/edit' : '/admin/services/create'
        );

        return [
            'name'             => $data['name'],
            'slug'             => $data['slug'],
            'summary'          => $data['summary'],
            'description'      => $data['description'] ?: null,
            'features'         => trim(str_replace("\r\n", "\n", (string) $data['features'])) ?: null,
            'price_from'       => round((float) $data['price_from'], 2),
            'duration_minutes' => (int) $data['duration_minutes'],
            'icon'             => $data['icon'] ?: 'wrench',
            'sort_order'       => (int) ($data['sort_order'] ?: 0),
            'is_package'       => $data['is_package'] === '1' ? 1 : 0,
            'is_active'        => $data['is_active'] === '1' ? 1 : 0,
        ];
    }
}

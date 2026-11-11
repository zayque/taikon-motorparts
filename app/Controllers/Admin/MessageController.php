<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\HttpException;
use App\Core\Request;
use App\Models\Message;

final class MessageController extends Controller
{
    public function index(): void
    {
        $filter = Request::query('filter') === 'unread' ? 'unread' : 'all';

        $this->view('admin/messages/index', [
            'title'    => 'Messages',
            'filter'   => $filter,
            'messages' => Message::paginate($filter, (int) config('catalog.per_page.admin')),
        ], 'admin');
    }

    public function show(string $id): void
    {
        $message = Message::find((int) $id) ?? throw HttpException::notFound();

        if (!$message['is_read']) {
            Message::markRead((int) $message['id']);
        }

        $this->view('admin/messages/show', ['title' => $message['subject'], 'message' => $message], 'admin');
    }

    public function destroy(string $id): void
    {
        Message::delete((int) $id);
        flash('success', 'Message deleted.');
        redirect('/admin/messages');
    }
}

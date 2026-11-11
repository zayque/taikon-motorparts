<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\Message;

final class PageController extends Controller
{
    public function about(): void
    {
        $this->view('pages/about', [
            'title'       => 'About us',
            'description' => 'Taikon Motorparts is a family-run motorcycle parts shop and workshop in Kuang, Selangor, serving riders since 2005.',
        ]);
    }

    public function contact(): void
    {
        $this->view('pages/contact', [
            'title'       => 'Contact us',
            'description' => 'Visit our workshop in Kuang, Rawang or send us a message.',
        ]);
    }

    public function sendMessage(): void
    {
        // Honeypot: real visitors never fill the hidden "website" field.
        if (Request::input('website', '') !== '') {
            redirect('/contact');
        }

        $data = Request::only(['name', 'email', 'phone', 'subject', 'body']);

        $this->validate($data, [
            'name'    => 'required|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'phone',
            'subject' => 'required|max:150',
            'body'    => 'required|min:10|max:3000',
        ], ['body' => 'Message']);

        Message::create($data);

        flash('success', 'Thanks, ' . $data['name'] . '! We usually reply within one working day.');
        redirect('/contact');
    }
}

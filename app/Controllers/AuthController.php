<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Models\User;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Sign in']);
    }

    public function login(): void
    {
        $data = Request::only(['email', 'password']);
        $this->validate($data, ['email' => 'required|email', 'password' => 'required']);

        if (Auth::tooManyAttempts($data['email'])) {
            Session::flashInput($data);
            flash('error', 'Too many sign-in attempts. Please wait 15 minutes and try again.');
            redirect('/login');
        }

        if (!Auth::attempt($data['email'], $data['password'])) {
            Session::flashInput($data);
            Session::flashErrors(['email' => 'These credentials do not match our records.']);
            redirect('/login');
        }

        $user = Auth::user();
        flash('success', 'Welcome back, ' . explode(' ', (string) $user['name'])[0] . '!');

        redirect_uri(Session::pull('intended'), Auth::isAdmin() ? '/admin' : '/account');
    }

    public function showRegister(): void
    {
        $this->view('auth/register', ['title' => 'Create an account']);
    }

    public function register(): void
    {
        $data = Request::only(['name', 'email', 'phone', 'password', 'password_confirmation']);

        $this->validate(
            $data,
            [
                'name'     => 'required|max:100',
                'email'    => 'required|email|max:150',
                'phone'    => 'phone',
                'password' => 'required|min:8|max:72|confirmed',
            ],
            [],
            static function ($v) use ($data) {
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) && User::emailTaken($data['email'])) {
                    $v->addError('email', 'An account with this email already exists.');
                }
            }
        );

        $id = User::create($data);
        Auth::login($id);

        flash('success', 'Your account is ready. Welcome to Taikon Motorparts!');
        redirect_uri(Session::pull('intended'), '/account');
    }

    public function logout(): void
    {
        Auth::logout();
        flash('success', 'You have been signed out.');
        redirect('/');
    }
}

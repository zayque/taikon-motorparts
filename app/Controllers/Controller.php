<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

abstract class Controller
{
    /** @param array<string, mixed> $data */
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        echo View::render($view, $data, $layout);
    }

    /**
     * Validate input; on failure flash errors + old input and go back.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @param array<string, string> $labels
     * @param (callable(Validator): void)|null $extra additional checks
     * @param string|null $formPath where the form lives, used when the browser sends no Referer
     *                              (defaults to the current path, which is right when GET and POST share a URL)
     */
    protected function validate(array $data, array $rules, array $labels = [], ?callable $extra = null, ?string $formPath = null): void
    {
        $validator = Validator::make($data, $rules, $labels);

        if ($extra !== null) {
            $extra($validator);
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($data);
            flash('error', 'Please check the highlighted fields.');
            back($formPath ?? Request::path());
        }
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
}

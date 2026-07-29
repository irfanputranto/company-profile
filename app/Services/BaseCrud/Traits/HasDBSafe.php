<?php

namespace App\Services\BaseCrud\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

trait HasDBSafe
{
    public $thMessage;

    public $thData;

    public $dbTrxDisable = false;

    public function DBSafe(callable $func): mixed
    {
        try {
            $data = $this->dbTrxDisable
                ? $func()
                : DB::transaction($func, 3);
        } catch (\Throwable $th) {
            if (! $th instanceof ValidationException) {
                report($th);
            }

            return $this->__errorDBSafe($th);
        }

        try {
            $this->__afterCommit();
        } catch (\Throwable $th) {
            report($th);
        }

        return $data;
    }

    public function __errorDBSafe(\Throwable $th): mixed
    {
        if ($th instanceof ValidationException) {
            $errors = $th->errors();

            if (request()->expectsJson()) {
                return json_error(__('errors.validation_failed'), 422, [
                    'errors' => $errors,
                ]);
            }

            return back()
                ->withInput()
                ->withErrors($errors)
                ->with('error_message', __('errors.validation_failed'));
        }

        if ($th instanceof AuthorizationException || $th instanceof ModelNotFoundException || $th instanceof HttpExceptionInterface) {
            throw $th;
        }

        $message = $this->thMessage ?? __('errors.unexpected');
        $errors = $this->thData ?? [];

        if (request()->expectsJson()) {
            return json_error($message, 500, [
                'errors' => $errors,
                'error_code' => 500,
            ]);
        }

        return back()->withInput()->with([
            'alert' => [
                'icon' => 'error',
                'title' => __('errors.unexpected_title'),
                'message' => $message,
            ],
            'errors' => $errors,
        ]);
    }

    public function __afterCommit(): void {}
}

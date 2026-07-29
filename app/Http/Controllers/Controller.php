<?php

namespace App\Http\Controllers;

use Carbon\CarbonInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Throwable;

abstract class Controller
{
    use AuthorizesRequests;

    protected function queryDate(Request $request, string $key): ?CarbonInterface
    {
        $value = $request->query($key);

        if (! is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return null;
        }

        try {
            $date = Date::createFromFormat('!Y-m-d', $value);

            return $date !== false && $date->format('Y-m-d') === $value ? $date : null;
        } catch (Throwable) {
            return null;
        }
    }
}

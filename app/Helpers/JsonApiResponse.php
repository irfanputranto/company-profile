<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

if (!function_exists('json_success')) {
    /**
     * Mengembalikan respons JSON sukses yang konsisten.
     *
     * @param mixed $data Data yang akan dikirim.
     * @param string $message Pesan sukses.
     * @param int $code Kode status HTTP.
     * @return JsonResponse
     */
    function json_success(mixed $data = [], string $message = 'Success', int $code = 200): JsonResponse
    {
        $pagination = null;

        if ($data instanceof LengthAwarePaginator || $data instanceof Paginator) {
            $pagination = $data;
        } elseif ($data instanceof AnonymousResourceCollection && $data->resource instanceof LengthAwarePaginator) {
            $pagination = $data->resource;
        }

        if ($pagination) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => $data->items(),
                'links'   => [
                    'first' => $pagination->url(1),
                    'last'  => $pagination->url($pagination->lastPage()),
                    'prev'  => $pagination->previousPageUrl(),
                    'next'  => $pagination->nextPageUrl(),
                ],
                'meta'    => [
                    'current_page' => $pagination->currentPage(),
                    'from'         => $pagination->firstItem(),
                    'last_page'    => $pagination->lastPage(),
                    'path'         => $pagination->path(),
                    'per_page'     => $pagination->perPage(),
                    'to'           => $pagination->lastItem(),
                    'total'        => $pagination->total(),
                ]
            ], $code);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}

if (!function_exists('json_error')) {
    /**
     * Mengembalikan respons JSON error yang konsisten.
     *
     * @param string $message Pesan error.
     * @param int $code Kode status HTTP.
     * @param array $errors Detail error (opsional, untuk validasi dll).
     * @return JsonResponse
     */
    function json_error(string $message = 'An error occurred', int $code = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}

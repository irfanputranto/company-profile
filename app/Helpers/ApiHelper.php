<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;

class ApiHelper
{
    /**
     * Fungsi utama untuk memanggil API dengan penanganan timeout khusus.
     *
     * @param string $method Metode HTTP (GET, POST, PUT, DELETE).
     * @param string $endpoint Endpoint API yang dituju.
     * @param array $data Data yang dikirim untuk metode POST atau PUT.
     * @param array $queryParams Parameter query untuk URL.
     * @param string|null $token Token otorisasi Bearer.
     * @param array $multipartData Data multipart untuk upload file.
     * @param int $timeout Waktu timeout dalam detik (default 60).
     * @param string|null $timeoutRedirectUrl URL untuk redirect saat timeout (jika null, hanya return error).
     * @return array|RedirectResponse Hasil dari pemanggilan API atau redirect response.
     */
    public static function call(
        string $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        ?string $token = null,
        array $multipartData = [],
        int $timeout = 60,
        ?string $timeoutRedirectUrl = null,
        array $extraHeaders = [],
    ) {
        $baseUrl = config('app.base_url_app_v2');

        if (!$baseUrl) {
            Log::error('API Helper Error: Base URL V2 tidak dikonfigurasi.');
            return [
                'success'     => false,
                'status_code' => 500,
                'error'       => ['message' => 'Konfigurasi base URL API tidak ditemukan.']
            ];
        }

        $fullUrl = $baseUrl . '/' . ltrim($endpoint, '/');

        $defaultHeaders = [
            'Accept' => 'application/json',
        ];

        $clientId = config('app.client_id_sso_v2');
        if ($clientId && empty($extraHeaders)) {
            $defaultHeaders['X-Client-Id'] = $clientId;
        }

        $headers = array_merge($defaultHeaders, $extraHeaders);

        $request = Http::withHeaders($headers);

        $request->timeout($timeout);

        $isMultipart = !empty($multipartData);
        if ($isMultipart) {
            $request->asMultipart();
            foreach ($multipartData as $name => $file) {
                if ($file instanceof UploadedFile) {
                    $request->attach($name, file_get_contents($file->getRealPath()), $file->getClientOriginalName());
                }
            }
        } else {
            $request->asJson();
        }

        if ($token) {
            $request->withToken($token);
        }

        if (!empty($queryParams)) {
            $request->withQueryParameters($queryParams);
        }

        try {
            $method = strtoupper($method);

            $response = match ($method) {
                'GET'    => $request->get($fullUrl),
                'POST'   => $request->post($fullUrl, $data),
                'PUT'    => $request->put($fullUrl, $data),
                'DELETE' => $request->delete($fullUrl, $data),
                default  => throw new Exception("Metode HTTP tidak valid: {$method}"),
            };

            if ($response->successful()) {
                return [
                    'success'     => true,
                    'status_code' => $response->status(),
                    'data'        => $response->json(),
                ];
            } else {
                Log::warning('API Call Failed', [
                    'url' => $fullUrl,
                    'method' => $method,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [
                    'success'     => false,
                    'status_code' => $response->status(),
                    'error'       => $response->json() ?? ['message' => $response->reason()]
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('API Connection Timeout: ' . $e->getMessage(), [
                'url' => $fullUrl,
                'method' => $method,
                'timeout' => $timeout
            ]);

            if ($timeoutRedirectUrl) {
                return Redirect::away($timeoutRedirectUrl)
                    ->with('error', 'Koneksi ke server API timeout. Silakan coba lagi nanti.')
                    ->with('timeout_url', $fullUrl);
            }

            return [
                'success'     => false,
                'status_code' => 504,
                'error'       => ['message' => 'Koneksi ke server API timeout. Silakan coba lagi nanti.']
            ];
        } catch (Exception $e) {
            Log::error('API Call Exception: ' . $e->getMessage(), [
                'url' => $fullUrl,
                'method' => $method,
            ]);
            return [
                'success'     => false,
                'status_code' => 500,
                'error'       => ['message' => 'Terjadi kesalahan saat menghubungi server API.', 'details' => $e->getMessage()]
            ];
        }
    }

    /**
     * Shortcut untuk memanggil API dengan timeout handling khusus.
     *
     * @param string $method Metode HTTP
     * @param string $endpoint Endpoint API
     * @param array $data Data request
     * @param array $queryParams Query parameters
     * @param string|null $token Token otorisasi
     * @param string|null $timeoutRedirectUrl URL untuk redirect saat timeout
     * @return array|RedirectResponse
     */
    public static function callWithTimeoutHandling(
        string $method,
        string $endpoint,
        array $data = [],
        array $queryParams = [],
        ?string $token = null,
        ?string $timeoutRedirectUrl = null,
        array $extraHeaders = [],
    ) {
        return self::call(
            $method,
            $endpoint,
            $data,
            $queryParams,
            $token,
            [],
            120,
            $timeoutRedirectUrl,
            $extraHeaders
        );
    }
}

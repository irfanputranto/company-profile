<?php

namespace App\Services\BaseCrud\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait HasCrudSuccessResult
{
    public function __successList($query)
    {
        $request = $this->requestData;

        $data = $this->prepareListData($query);

        if ($request->query("is_cache") == "1") {
            $key = $request->getRequestUri();
            Cache::put($key, $data, Carbon::now()->addMinutes($this->cacheInMinute));
        }

        return json_success($data, 'Data loaded successfully');
    }

    public function __successPrint($query)
    {
        $request = $this->requestData;

        $data = $this->prepareListData($query);

        if ($request->query("is_cache") == "1") {
            $key = $request->getRequestUri();
            Cache::put($key, $data, Carbon::now()->addMinutes($this->cacheInMinute));
        }

        return json_success($data, 'Data loaded successfully');
    }

    protected function prepareListData($query)
    {
        $data = [
            'data' => [],
            'meta' => $this->getPaginationMeta($query)
        ];

        if ($query instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $data['data'] = $query->items();
        } elseif (is_array($query)) {
            $data['data'] = $query;
        } elseif ($query instanceof \Illuminate\Support\Collection) {
            $data['data'] = $query->all();
        } else {
            $data['data'] = collect($query)->all();
        }

        // Jika ada resource, gunakan format resource
        if (isset($this->resource)) {
            $data['data'] = $this->resource::collection($data['data'])->toArray($this->requestData);
        }

        return $data;
    }

    protected function getPaginationMeta($query)
    {
        if ($query instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [
                'current_page' => $query->currentPage(),
                'from' => $query->firstItem(),
                'last_page' => $query->lastPage(),
                'per_page' => $query->perPage(),
                'to' => $query->lastItem(),
                'total' => $query->total(),
            ];
        }

        // Untuk data API atau array biasa
        if (isset($this->pagination)) {
            return $this->pagination;
        }

        return [
            'current_page' => 1,
            'from' => 0,
            'last_page' => 1,
            'per_page' => $this->paginationPerPage,
            'to' => 0,
            'total' => is_array($query) ? count($query) : 0,
        ];
    }

    public function __successShow()
    {
        $request = $this->requestData;

        $data = new $this->resource($this->row);

        if ($request->query("is_cache") == "1") {
            $key = $request->getRequestUri();
            Cache::put($key, $data, Carbon::now()->addMinutes($this->cacheInMinute));
        }

        return json_success($data, 'Data loaded successfully');
    }

    public function __successUpdate()
    {
        if ($this->redirectSuccessUpdate) {
            return $this->__redirectSuccess()->with('success_message', $this->successUpdateMsg);
        }

        return json_success(new $this->resource($this->row), $this->successUpdateMsg);
    }

    public function __successStore()
    {
        return json_success(new $this->resource($this->row), $this->successStoreMsg);
    }

    public function __successDestroy()
    {
        return json_success(['success' => true], $this->successDestroyMsg);
    }

    public function __successBulkDestroy()
    {
        return json_success(['success' => true], $this->successDestroyMsg);
    }
}

trait HasCrudErrorResult
{
    public function __errorList($e)
    {
        return $this->formatErrorResponse($e, 'index');
    }

    public function __errorShow($e)
    {
        return $this->formatErrorResponse($e, 'show');
    }

    public function __errorStore($e)
    {
        return $this->formatErrorResponse($e, 'store');
    }

    public function __errorUpdate($e)
    {
        return $this->formatErrorResponse($e, 'update');
    }

    public function __errorDestroy($e)
    {
        return $this->formatErrorResponse($e, 'destroy');
    }

    public function __errorBulkDestroy($e)
    {
        return $this->formatErrorResponse($e, 'bulk_destroy');
    }

    protected function formatErrorResponse($e, $type)
    {
        $errorData = [
            'success' => false,
            'message' => $this->getErrorMessage($type),
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'type' => $type
        ];

        return json_error($errorData['message'], 500, $errorData);
    }

    protected function getErrorMessage($type)
    {
        $messages = [
            'index' => $this->errorIndexMsg ?? 'Failed to load data!',
            'show' => $this->errorShowMsg ?? 'Failed to show data!',
            'store' => $this->errorStoreMsg ?? 'Failed to create data!',
            'update' => $this->errorUpdateMsg ?? 'Failed to update data!',
            'destroy' => $this->errorDestroyMsg ?? 'Failed to delete data!',
            'bulk_destroy' => $this->errorDestroyMsg ?? 'Failed to delete data!'
        ];

        return $messages[$type] ?? 'An error occurred!';
    }

    public function __errorValidation($errors)
    {
        return json_error('Validation failed', 422, $errors);
    }
}

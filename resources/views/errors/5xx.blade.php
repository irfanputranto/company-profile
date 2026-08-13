@php
    $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
@endphp

<x-error-page :code="$statusCode" :title="__('errors.pages.5xx.title')" :message="__('errors.pages.5xx.message')" retry />

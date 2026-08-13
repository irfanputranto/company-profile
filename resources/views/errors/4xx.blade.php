@php
    $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400;
@endphp

<x-error-page :code="$statusCode" :title="__('errors.pages.4xx.title')" :message="__('errors.pages.4xx.message')" />

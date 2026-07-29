<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CompanyProfile\Requests\StoreAnalyticsEventRequest;
use App\Modules\CompanyProfile\Services\VisitRecorder;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

use function Illuminate\Support\defer;

class PublicAnalyticsController extends Controller
{
    public function __invoke(
        StoreAnalyticsEventRequest $request,
        VisitRecorder $visitRecorder,
    ): Response {
        $validated = $request->validated();
        $scopeType = (string) $validated['scope_type'];
        $event = (string) $validated['event'];
        $scopeId = (int) config("analytics.events.{$scopeType}.{$event}");
        $context = $visitRecorder->context($request);
        $referrerPath = parse_url((string) $request->headers->get('referer'), PHP_URL_PATH);

        $context['route_name'] = "analytics.{$scopeType}.{$event}";
        $context['path'] = is_string($referrerPath) && $referrerPath !== ''
            ? Str::limit($referrerPath, 2048, '')
            : '/';

        defer(
            fn () => $visitRecorder->record($context, $scopeType, $scopeId)
        )->name("record-{$scopeType}-{$event}");

        return response()->noContent();
    }
}

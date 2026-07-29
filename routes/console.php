<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$visitAggregation = Schedule::command('visits:aggregate')->hourly();

if (app()->environment('production')) {
    $visitAggregation
        ->withoutOverlapping(55)
        ->onOneServer();
}

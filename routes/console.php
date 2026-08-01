<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$visitAggregation = Schedule::command('visits:aggregate')->hourly();
$serverExpiryReminders = Schedule::command('projects:send-server-expiry-reminders')->dailyAt('08:00')->withoutOverlapping();

if (app()->environment('production')) {
    $visitAggregation
        ->withoutOverlapping(55)
        ->onOneServer();

    $serverExpiryReminders->onOneServer();
}

<?php

use App\Jobs\SyncLatestEmailJob;
use App\Jobs\ScanEmailJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    SyncLatestEmailJob::dispatch(); // dispatch ke queue
})->name('sync-latest-emails')->everyThirtyMinutes()->withoutOverlapping();


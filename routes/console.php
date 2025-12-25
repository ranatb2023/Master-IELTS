<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule package expiration check to run daily at midnight
Schedule::command('packages:expire-access')->daily()->at('00:00');

// Suspend expired subscriptions daily
Schedule::command('subscriptions:suspend-expired')->daily()->at('00:30');

<?php

use App\Console\Commands\UpdateProductsPoints;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

app(Schedule::class)->call(function () {
    (new UpdateProductsPoints)->handle();
})->everyMinute();

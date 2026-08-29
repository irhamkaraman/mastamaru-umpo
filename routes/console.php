<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('umpo:sync-mahasiswa', function () {
    $this->call(\App\Console\Commands\SyncUmpoMahasiswa::class);
})->purpose('Sync active students from UMPO API for year 2026');


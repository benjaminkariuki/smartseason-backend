<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/run-seeders', function () {
    try {
        Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => true
        ]);
        return 'Database Migrated and Seeded Successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
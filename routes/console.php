<?php

use App\Services\MySqlExportService;
use Illuminate\Support\Facades\Artisan;

Artisan::command('lab:export-mysql', function () {
    $path = app(MySqlExportService::class)->export();
    $this->info('MySQL export written to: '.$path);
    $this->info('Import it once into a new, empty InfinityFree database. Copy avatar files with the application.');
})->purpose('Export current customer and user records for InfinityFree MySQL');

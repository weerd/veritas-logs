<?php

use Illuminate\Support\Facades\Route;

Route::get('veritas', function() {
    return 'Veritas Logs routes are working.';
});

Route::group(['namespace' => 'Weerd\VeritasLogs\Http\Controllers\Admin', 'prefix' => 'admin'], function() {
    Route::get('logs', 'LogController');
});

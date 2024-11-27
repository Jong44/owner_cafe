<?php

use App\Http\Controllers\SellingReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/member/selling-report/generate', SellingReportController::class)
    ->name('selling-report.generate');

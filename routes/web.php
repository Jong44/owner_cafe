<?php

use App\Http\Controllers\SellingReportController;
use Illuminate\Support\Facades\Route;


Route::get('/download-report', function () {
    $data = session('report_data');
    if (!$data) {
        abort(404);
    }
    $pdf = \PDF::loadView('filament.pages.report-outlet_1_pdf', $data);
    return $pdf->download('report_penjualan.pdf');
})->name('report.download')->middleware('web');
Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/member/selling-report/generate', SellingReportController::class)
    ->name('selling-report.generate');

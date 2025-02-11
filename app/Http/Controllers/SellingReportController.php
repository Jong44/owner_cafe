<?php

namespace App\Http\Controllers;

use App\Models\SettingOutlet1;
use App\Services\SellingOutletService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SellingReportController extends Controller
{
    public function __invoke(Request $request, SellingOutletService $sellingReportService)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $tax_data = (float) SettingOutlet1::get('default_tax', 0);

        $reportData = $sellingReportService->generate($request->all());
        $reports = $reportData['reports'];
        $footer = $reportData['footer'];
        $header = $reportData['header'];
        $tax = $tax_data;

        $pdf = Pdf::loadView('partials.selling', compact('reports', 'footer', 'header', 'tax'))
            ->setPaper('a4', 'landscape');
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(720, 570, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', null, 10, [0, 0, 0]);

        if ($request->ajax()) {
            return $pdf->download();
        }

        return $pdf->stream();
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\SellingOutlet1Report;
use App\Services\SellingAllOutletService;
use App\Services\SellingOutlet1Service;
use Filament\Widgets\Widget;

class SellingOutlet1Table extends Widget
{
    public $reports = null;

    public function mount(): void
    {
        $this->generate(new SellingAllOutletService);
    }

    public function getViewData(): array
    {
        return [
            'reports' => $this->reports,
        ];
    }
    protected static string $view = 'filament.widgets.selling-outlet1-table';

    public function generate(SellingAllOutletService $sellingReportService)
    {
       $this->reports = $sellingReportService->generate();
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\SettingOutlet1;
use App\Services\SellingOutletService;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class ReportOutlet_1 extends Page implements HasActions, HasForms
{
    use InteractsWithFormActions, InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Outlet 1';

    protected static ?string $title = 'Report Penjualan';

    public float $tax;

    protected static string $view = 'filament.pages.report-outlet_1';

    public ?array $data = [
        'start_date' => null,
        'end_date' => null,
        'outlet_id' => 1,
    ];

    public $reports = null;

    public function mount()
    {
        // Initial setup if needed
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('start_date')
                ->translateLabel()
                ->date()
                ->required()
                ->closeOnDateSelection()
                ->default(now())
                ->native(false),
            DatePicker::make('end_date')
                ->translateLabel()
                ->date()
                ->required()
                ->closeOnDateSelection()
                ->default(now())
                ->native(false),
        ])
        ->columns(2)
        ->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make(__('Generate'))
                ->action('generate'),
            Action::make(__('Print'))
                ->color('warning')
                ->extraAttributes([
                    'id' => 'print-btn',
                ])
                ->icon('heroicon-o-printer'),
            Action::make('download-pdf')
                ->label(__('Download as PDF'))
                ->action('downloadPdf')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-on-square'),
        ];
    }

    public function generate(SellingOutletService $sellingReportService)
    {
        $this->validate([
            'data.start_date' => 'required',
            'data.end_date' => 'required',
        ]);

        // Getting tax settings from database
        $this->tax = (float) SettingOutlet1::get('default_tax', 0);

        // Generate the report using the SellingOutletService
        $this->reports = $sellingReportService->generate($this->data);

        // Add tax information to the reports
        $this->reports = array_merge(
            $this->reports,
            [
                'tax' => $this->tax, // Adding tax value to reports
            ]
        );
    }

    public function downloadPdf()
    {
        $this->validate([
            'data.start_date' => 'required',
            'data.end_date' => 'required',
        ]);
    
        $this->tax = (float) SettingOutlet1::get('default_tax', 0);
        $this->reports = app(SellingOutletService::class)->generate($this->data);
        $footer = $this->reports['footer'] ?? [];
        $header = array_merge($this->data, ['shop_name' => 'Nama Toko Outlet 1']);
    
        $pdf = \PDF::loadView('filament.pages.report-outlet_1_pdf', [
            'header'  => $header,
            'reports' => $this->reports['reports'] ?? [],
            'footer'  => $footer,
            'tax'     => $this->tax,
        ]);
    
        $fileName = 'report_penjualan.pdf';
        $filePath = storage_path('app/public/' . $fileName);
        $pdf->save($filePath);
    
        // Pastikan folder public/storage sudah di-link (php artisan storage:link)
        return redirect()->to(asset('storage/' . $fileName));
    }
        public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public function getColumnStart(): int | string | array
    {
        return 'full';
    }
}

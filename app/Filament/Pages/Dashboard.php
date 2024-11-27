<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SellingOutlet1Table;
use App\Filament\Widgets\StatsRevenue;
use App\Filament\Widgets\StatsSelling;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard

{    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function getColumns(): array|int|string
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            StatsRevenue::class,
            StatsSelling::class,
            SellingOutlet1Table::class,
        ];
    }


}

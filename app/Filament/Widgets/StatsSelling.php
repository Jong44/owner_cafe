<?php

namespace App\Filament\Widgets;

use App\Models\ProfileOutlet1;
use App\Models\SellingOutlet1;
use App\Models\SellingOutlet2;
use App\Models\SellingOutlet3;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsSelling extends BaseWidget
{
    protected function getStats(): array
    {
        $todaySales = $this->getSalesToday();
        return [
            Stat::make('Sales Today Outlet 1', $todaySales['outlet1']),
            Stat::make('Sales Today Outlet 2', $todaySales['outlet2']),
            Stat::make('Sales Today Outlet 3', $todaySales['outlet3']),
            Stat::make('Sales Today All Outlet', $todaySales['all']),
        ];
    }

     private function getSalesToday()
    {
        $carbon = now();
        $today = $carbon->startOfDay()->format('Y-m-d H:i:s e');
        $startDate = Carbon::parse($today)->setTimezone( 'UTC');

        $salesTodayOutlet1 = SellingOutlet1::whereDate('date', $startDate)->count() ?? 0;
        $salesTodayOutlet2 = SellingOutlet2::whereDate('date', $startDate)->count() ?? 0;
        $salesTodayOutlet3 = SellingOutlet3::whereDate('date', $startDate)->count() ?? 0;

        return [
            'outlet1' => $salesTodayOutlet1,
            'outlet2' => $salesTodayOutlet2,
            'outlet3' => $salesTodayOutlet3,
            'all' => $salesTodayOutlet1 + $salesTodayOutlet2 + $salesTodayOutlet3,
        ];
    }
}

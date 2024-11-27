<?php

namespace App\Filament\Widgets;

use App\Models\SellingOutlet1;
use App\Models\SellingOutlet2;
use App\Models\SellingOutlet3;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class StatsRevenue extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue1 = $this->getTotalRevenue1();
        $totalRevenue2 = $this->getTotalRevenue2();
        $totalRevenue3 = $this->getTotalRevenue3();
        $totalRevenueAll = $this->getAllTotalRevenue();
        return [
            Stat::make(__('Today total revenue Outlet1'), $totalRevenue1['total_revenue'])
            ->descriptionIcon($totalRevenue1['icon'])
            ->description($totalRevenue1['description'])
            ->chart([$totalRevenue1['yesterdayRevenue'], $totalRevenue1['todayRevenue']])
            ->color($totalRevenue1['color']),
            Stat::make(__('Today total revenue Outlet2'), $totalRevenue2['total_revenue'])
            ->descriptionIcon($totalRevenue2['icon'])
            ->description($totalRevenue2['description'])
            ->chart([$totalRevenue2['yesterdayRevenue'], $totalRevenue2['todayRevenue']])
            ->color($totalRevenue2['color']),
            Stat::make(__('Today total revenue Outlet2'), $totalRevenue3['total_revenue'])
            ->descriptionIcon($totalRevenue3['icon'])
            ->description($totalRevenue3['description'])
            ->chart([$totalRevenue3['yesterdayRevenue'], $totalRevenue3['todayRevenue']])
            ->color($totalRevenue3['color']),
            Stat::make(__('Today total revenue All Outlet'), $totalRevenueAll['total_revenue'])
            ->descriptionIcon($totalRevenueAll['icon'])
            ->description($totalRevenueAll['description'])
            ->chart([$totalRevenueAll['yesterdayRevenue'], $totalRevenueAll['todayRevenue']])
            ->color($totalRevenueAll['color']),
        ];
    }

    private function getTotalRevenue1()
    {
        $carbon = now()->timezone('Asia/Jakarta');
        $startOfDay = $carbon->startOfDay();
        $startOfYesterday = $startOfDay->copy()->subDay();

        $yesterdayRevenue = $this->calculateRevenue1($startOfYesterday, $startOfDay);
        $todayRevenue = $this->calculateRevenue1($startOfDay, $startOfDay->copy()->addDay());

        $totalYesterdayRevenue = $this->calculateTotalRevenue($yesterdayRevenue);
        $totalTodayRevenue = $this->calculateTotalRevenue($todayRevenue);

        $readable = $this->getReadableSuffix($totalTodayRevenue);

        $trendData = $this->getTrendData($totalYesterdayRevenue, $totalTodayRevenue);

        $percentage = $totalYesterdayRevenue ? (($totalTodayRevenue - $totalYesterdayRevenue) / $totalYesterdayRevenue) * 100 : 0;

        return [
            'total_revenue' => $readable,
            'description' => round($percentage).'% '.$trendData['trend'],
            'yesterdayRevenue' => $totalYesterdayRevenue,
            'todayRevenue' => $totalTodayRevenue,
            'color' => $trendData['color'],
            'icon' => $trendData['icon'],
        ];
    }
    private function getTotalRevenue2()
    {
        $carbon = now()->timezone('Asia/Jakarta');
        $startOfDay = $carbon->startOfDay();
        $startOfYesterday = $startOfDay->copy()->subDay();

        $yesterdayRevenue = $this->calculateRevenue2($startOfYesterday, $startOfDay);
        $todayRevenue = $this->calculateRevenue2($startOfDay, $startOfDay->copy()->addDay());

        $totalYesterdayRevenue = $this->calculateTotalRevenue($yesterdayRevenue);
        $totalTodayRevenue = $this->calculateTotalRevenue($todayRevenue);

        $readable = $this->getReadableSuffix($totalTodayRevenue);

        $trendData = $this->getTrendData($totalYesterdayRevenue, $totalTodayRevenue);

        $percentage = $totalYesterdayRevenue ? (($totalTodayRevenue - $totalYesterdayRevenue) / $totalYesterdayRevenue) * 100 : 0;

        return [
            'total_revenue' => $readable,
            'description' => round($percentage).'% '.$trendData['trend'],
            'yesterdayRevenue' => $totalYesterdayRevenue,
            'todayRevenue' => $totalTodayRevenue,
            'color' => $trendData['color'],
            'icon' => $trendData['icon'],
        ];
    }
    private function getTotalRevenue3()
    {
        $carbon = now()->timezone('Asia/Jakarta');
        $startOfDay = $carbon->startOfDay();
        $startOfYesterday = $startOfDay->copy()->subDay();

        $yesterdayRevenue = $this->calculateRevenue3($startOfYesterday, $startOfDay);
        $todayRevenue = $this->calculateRevenue3($startOfDay, $startOfDay->copy()->addDay());

        $totalYesterdayRevenue = $this->calculateTotalRevenue($yesterdayRevenue);
        $totalTodayRevenue = $this->calculateTotalRevenue($todayRevenue);

        $readable = $this->getReadableSuffix($totalTodayRevenue);

        $trendData = $this->getTrendData($totalYesterdayRevenue, $totalTodayRevenue);

        $percentage = $totalYesterdayRevenue ? (($totalTodayRevenue - $totalYesterdayRevenue) / $totalYesterdayRevenue) * 100 : 0;

        return [
            'total_revenue' => $readable,
            'description' => round($percentage).'% '.$trendData['trend'],
            'yesterdayRevenue' => $totalYesterdayRevenue,
            'todayRevenue' => $totalTodayRevenue,
            'color' => $trendData['color'],
            'icon' => $trendData['icon'],
        ];
    }

    private function getAllTotalRevenue()
    {
        $carbon = now()->timezone('Asia/Jakarta');
        $startOfDay = $carbon->startOfDay();
        $startOfYesterday = $startOfDay->copy()->subDay();

        $yesterdayRevenue1 = $this->calculateRevenue1($startOfYesterday, $startOfDay);
        $todayRevenue1 = $this->calculateRevenue1($startOfDay, $startOfDay->copy()->addDay());

        $yesterdayRevenue2 = $this->calculateRevenue2($startOfYesterday, $startOfDay);
        $todayRevenue2 = $this->calculateRevenue2($startOfDay, $startOfDay->copy()->addDay());

        $yesterdayRevenue3 = $this->calculateRevenue3($startOfYesterday, $startOfDay);
        $todayRevenue3 = $this->calculateRevenue3($startOfDay, $startOfDay->copy()->addDay());

        $totalYesterdayRevenue1 = $this->calculateTotalRevenue($yesterdayRevenue1);
        $totalTodayRevenue1 = $this->calculateTotalRevenue($todayRevenue1);

        $totalYesterdayRevenue2 = $this->calculateTotalRevenue($yesterdayRevenue2);
        $totalTodayRevenue2 = $this->calculateTotalRevenue($todayRevenue2);

        $totalYesterdayRevenue3 = $this->calculateTotalRevenue($yesterdayRevenue3);
        $totalTodayRevenue3 = $this->calculateTotalRevenue($todayRevenue3);

        $totalYesterdayRevenue = $totalYesterdayRevenue1 + $totalYesterdayRevenue2 + $totalYesterdayRevenue3;
        $totalTodayRevenue = $totalTodayRevenue1 + $totalTodayRevenue2 + $totalTodayRevenue3;

        $readable = $this->getReadableSuffix($totalTodayRevenue);

        $trendData = $this->getTrendData($totalYesterdayRevenue, $totalTodayRevenue);

        $percentage = $totalYesterdayRevenue ? (($totalTodayRevenue - $totalYesterdayRevenue) / $totalYesterdayRevenue) * 100 : 0;

        return [
            'total_revenue' => $readable,
            'description' => round($percentage).'% '.$trendData['trend'],
            'yesterdayRevenue' => $totalYesterdayRevenue,
            'todayRevenue' => $totalTodayRevenue,
            'color' => $trendData['color'],
            'icon' => $trendData['icon'],
        ];
    }

    private function calculateRevenue1($start, $end)
    {
        return SellingOutlet1::query()
            ->select(
                DB::raw('SUM(sellings.discount_price) as discount_price'),
                DB::raw('SUM(sellings.total_discount_per_item) as total_discount_per_item'),
                DB::raw('SUM(sellings.tax_price) as tax_price'),
                DB::raw('SUM(sellings.total_price) as total_price'),
                DB::raw('SUM(sellings.total_cost) as total_cost'),
            )
            ->isPaid()
            ->whereBetween('sellings.created_at', [
                $start->setTimezone('UTC'),
                $end->setTimezone('UTC'),
            ])
            ->first();
    }
    private function calculateRevenue2($start, $end)
    {
        return SellingOutlet2::query()
            ->select(
                DB::raw('SUM(sellings.discount_price) as discount_price'),
                DB::raw('SUM(sellings.total_discount_per_item) as total_discount_per_item'),
                DB::raw('SUM(sellings.tax_price) as tax_price'),
                DB::raw('SUM(sellings.total_price) as total_price'),
                DB::raw('SUM(sellings.total_cost) as total_cost'),
            )
            ->isPaid()
            ->whereBetween('sellings.created_at', [
                $start->setTimezone('UTC'),
                $end->setTimezone('UTC'),
            ])
            ->first();
    }
    private function calculateRevenue3($start, $end)
    {
        return SellingOutlet3::query()
            ->select(
                DB::raw('SUM(sellings.discount_price) as discount_price'),
                DB::raw('SUM(sellings.total_discount_per_item) as total_discount_per_item'),
                DB::raw('SUM(sellings.tax_price) as tax_price'),
                DB::raw('SUM(sellings.total_price) as total_price'),
                DB::raw('SUM(sellings.total_cost) as total_cost'),
            )
            ->isPaid()
            ->whereBetween('sellings.created_at', [
                $start->setTimezone('UTC'),
                $end->setTimezone('UTC'),
            ])
            ->first();
    }

    private function calculateTotalRevenue($revenue)
    {
        $grossProfit = $revenue->total_price - $revenue->tax_price - $revenue->total_discount_per_item - $revenue->discount_price;

        return $grossProfit - $revenue->total_cost;
    }

    private function getReadableSuffix($totalRevenue)
    {
        return Number::abbreviate($totalRevenue);
    }

    private function getTrendData($totalYesterdayRevenue, $totalTodayRevenue)
    {
        if ($totalYesterdayRevenue > $totalTodayRevenue) {
            return [
                'trend' => __('decrease'),
                'color' => 'danger',
                'icon' => 'heroicon-m-arrow-trending-down',
            ];
        }

        if ($totalYesterdayRevenue < $totalTodayRevenue) {
            return [
                'trend' => __('increase'),
                'color' => 'success',
                'icon' => 'heroicon-m-arrow-trending-up',
            ];
        }

        return [
            'trend' => __('sideway'),
            'color' => 'warning',
            'icon' => 'heroicon-m-minus',
        ];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class SellingAllOutletService
{
    protected $outlets = [
        1 => [
            'selling' => 'App\Models\SellingOutlet1',
            'details' => 'App\Models\SellingDetailOutlet1',
        ],
        2 => [
            'selling' => 'App\Models\SellingOutlet2',
            'details' => 'App\Models\SellingDetailOutlet2',
        ],
        3 => [
            'selling' => 'App\Models\SellingOutlet3',
            'details' => 'App\Models\SellingDetailOutlet3',
        ],
    ];

    public function generate()
    {
        $carbon = now();
        $allReports = [];


        $totalQty = 0;
        $totalCost = 0;
        $totalGross = 0;
        $totalNet = 0;
        $totalGrossProfit = 0;
        $totalDiscount = 0;
        $totalDiscountPerItem = 0;
        $totalNetProfitBeforeDiscountSelling = 0;
        $totalNetProfitAfterDiscountSelling = 0;

        foreach ($this->outlets as $outletId => $models) {
            $sellingModel = $models['selling'];
            $detailModel = $models['details'];

            $sellings = $sellingModel::query()
                ->select()
                ->with(
                    'sellingDetails:id,selling_id,product_id,qty,price,cost,discount_price',
                    'sellingDetails.product:id,name,initial_price,selling_price,sku'
                )
                ->when($carbon, function ($query) use ($carbon) {
                    $query->whereDate('created_at', $carbon->format('Y-m-d'));
                })
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($sellings as $selling) {
                $totalDiscountPerItem = 0;
                $totalBeforeDiscountPerSelling = 0;
                $totalAfterDiscountPerSelling = 0;
                $totalNetProfitPerSelling = 0;
                $totalGrossProfitPerSelling = 0;
                $totalCostPerSelling = 0;
                $totalQtyPerSelling = 0;

                foreach ($selling->sellingDetails as $detail) {
                    $totalDiscountPerItem += ($detail->discount_price ?? 0);
                    $totalBeforeDiscountPerSelling += $detail->price;
                    $totalAfterDiscountPerSelling += ($detail->price - ($detail->discount_price ?? 0));
                    $totalNetProfitPerSelling += (($detail->price - $detail->cost) - ($detail->discount_price ?? 0));
                    $totalGrossProfitPerSelling += ($detail->price - $detail->cost);
                    $totalCostPerSelling += $detail->cost;
                    $totalQtyPerSelling += $detail->qty;

                    $allReports[] = [
                        'outlet' => $outletId,
                        'id' => $selling->id,
                        'code' => $selling->code,
                        'sku' => $detail->product->sku,
                        'name' => $detail->product->name,
                        'selling_price' => $this->formatCurrency($detail->price / $detail->qty),
                        'selling' => $this->formatCurrency($detail->price - ($detail->discount_price ?? 0)),
                        'discount_price' => $this->formatCurrency($detail->discount_price ?? 0),
                        'initial_price' => $this->formatCurrency($detail->cost / $detail->qty),
                        'qty' => $detail->qty,
                        'cost' => $detail->cost,
                        'total_after_discount' => $this->formatCurrency($detail->price - ($detail->discount_price ?? 0)),
                        'net_profit' => $this->formatCurrency(($detail->price - ($detail->discount_price ?? 0)) - $detail->cost),
                        'gross_profit' => $this->formatCurrency($detail->price - $detail->cost),
                        'created_at' => $selling->created_at,
                    ];
                }
            }

            $totalCost += $totalCostPerSelling;
            $totalDiscount += ($selling->discount_price ?? 0);
            $totalGross += $totalBeforeDiscountPerSelling;
            $totalNet += $totalAfterDiscountPerSelling;
            $totalNetProfitBeforeDiscountSelling += $totalNetProfitPerSelling;
            $totalNetProfitAfterDiscountSelling += ($totalNetProfitPerSelling - ($selling->discount_price ?? 0));
            $totalGrossProfit += $totalGrossProfitPerSelling;
            $totalDiscountPerItem += $totalDiscountPerItem;
            $totalQty += $totalQtyPerSelling;

            usort($allReports, function ($a, $b) {
                return $b['created_at'] <=> $a['created_at'];
            });
        }

        $footer = [
            'total_cost' => $this->formatCurrency($totalCost),
            'total_gross' => $this->formatCurrency($totalGross),
            'total_net' => $this->formatCurrency($totalNet - $totalDiscount),
            'total_discount' => $this->formatCurrency($totalDiscount),
            'total_discount_per_item' => $this->formatCurrency($totalDiscountPerItem),
            'total_gross_profit' => $this->formatCurrency($totalGross - $totalCost),
            'total_net_profit_before_discount_selling' => $this->formatCurrency($totalNet - $totalCost),
            'total_net_profit_after_discount_selling' => $this->formatCurrency($totalNet - $totalDiscount - $totalCost),
            'total_qty' => $totalQty,
        ];



        return [
            'reports' => $allReports,
            'footer' => $footer,
        ];
    }

    private function formatCurrency($value)
    {
        return Number::format($value);
    }
}

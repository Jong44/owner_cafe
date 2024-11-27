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
                foreach ($selling->sellingDetails as $detail) {
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
                    ];
                }
            }
        }

        return [
            'reports' => $allReports,
        ];
    }

    private function formatCurrency($value)
    {
        return Number::format($value);
    }
}

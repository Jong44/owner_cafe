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
        $carbon = now()->timezone('Asia/Jakarta');
        $allReports = [];

        // Initialize total variables
        $totalQty = 0;
        $totalCost = 0;
        $totalGross = 0;
        $totalNet = 0;
        $totalGrossProfit = 0;
        $totalDiscount = 0;
        $totalDiscountPerItem = 0;
        $totalNetProfitBeforeDiscountSelling = 0;
        $totalNetProfitAfterDiscountSelling = 0;

        $paymentMethodTotals = [];  // Store totals per payment method

        foreach ($this->outlets as $outletId => $models) {
            $sellingModel = $models['selling'];
            $detailModel = $models['details'];

            // Fetch sellings with related payment method and selling details
            $sellings = $sellingModel::query()
                ->select()
                ->with(
                    'sellingDetails:id,selling_id,product_id,qty,price,cost,discount_price',
                    'sellingDetails.product:id,name,initial_price,selling_price,sku',
                    'paymentMethod:id,name'  // Eager load payment method
                )
                ->when($carbon, function ($query) use ($carbon) {
                    $query->whereDate('created_at', $carbon->format('Y-m-d'));
                })
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($sellings as $selling) {
                // Safely access payment method
                $paymentMethodName = $selling->paymentMethod ? $selling->paymentMethod->name : 'Unknown';

                $totalDiscountPerItem = 0;
                $totalBeforeDiscountPerSelling = 0;
                $totalAfterDiscountPerSelling = 0;
                $totalNetProfitPerSelling = 0;
                $totalGrossProfitPerSelling = 0;
                $totalCostPerSelling = 0;
                $totalQtyPerSelling = 0;

                foreach ($selling->sellingDetails as $detail) {
                    // Sum up values per selling detail
                    $totalDiscountPerItem += ($detail->discount_price ?? 0);
                    $totalBeforeDiscountPerSelling += $detail->price;
                    $totalAfterDiscountPerSelling += ($detail->price - ($detail->discount_price ?? 0));
                    $totalNetProfitPerSelling += (($detail->price - $detail->cost) - ($detail->discount_price ?? 0));
                    $totalGrossProfitPerSelling += ($detail->price - $detail->cost);
                    $totalCostPerSelling += $detail->cost;
                    $totalQtyPerSelling += $detail->qty;

                    // Store the individual report details
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
                        'payment_method' => $paymentMethodName,  // Ensure payment method is added
                    ];
                }

                // Sum totals for payment method
                if (!isset($paymentMethodTotals[$paymentMethodName])) {
                    $paymentMethodTotals[$paymentMethodName] = [
                        'total_sales' => 0,
                        'total_with_tax' => 0,
                    ];
                }

                $paymentMethodTotals[$paymentMethodName]['total_sales'] += $totalBeforeDiscountPerSelling;
                $paymentMethodTotals[$paymentMethodName]['total_with_tax'] += $totalAfterDiscountPerSelling;

                // Sum overall totals
                $totalCost += $totalCostPerSelling;
                $totalDiscount += ($selling->discount_price ?? 0);
                $totalGross += $totalBeforeDiscountPerSelling;
                $totalNet += $totalAfterDiscountPerSelling;
                $totalNetProfitBeforeDiscountSelling += $totalNetProfitPerSelling;
                $totalNetProfitAfterDiscountSelling += ($totalNetProfitPerSelling - ($selling->discount_price ?? 0));
                $totalGrossProfit += $totalGrossProfitPerSelling;
                $totalDiscountPerItem += $totalDiscountPerItem;
                $totalQty += $totalQtyPerSelling;
            }

            // Sort reports by created_at
            usort($allReports, function ($a, $b) {
                return $b['created_at'] <=> $a['created_at'];
            });
        }

        // Footer with payment method totals
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
            'payment_method_totals' => $paymentMethodTotals, // Add payment method totals to footer
        ];

        return [
            'reports' => $allReports,
            'footer' => $footer,
        ];
    }

    private function formatCurrency($value)
    {
        return Number::format($value);  // Format numbers according to your needs
    }
}

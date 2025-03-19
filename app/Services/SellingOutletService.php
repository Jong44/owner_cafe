<?php

namespace App\Services;

use App\Models\AboutOutlet1;
use App\Models\AboutOutlet2;
use App\Models\AboutOutlet3;
use App\Models\SellingDetailOutlet1;
use App\Models\SellingOutlet1;
use App\Models\SellingOutlet2;
use App\Models\SellingOutlet3;
use App\Models\SettingOutlet1;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class SellingOutletService
{
    protected $outlets = [
        1 => [
            'models' => 'App\Models\SellingOutlet1',
        ],
        2 => [
            'models' => 'App\Models\SellingOutlet2',
        ],
        3 => [
            'models' => 'App\Models\SellingOutlet3',
        ],
    ];

    public function generate(array $data)
    {
        $timezone = 'Asia/Jakarta';
    
        $startDate = Carbon::parse($data['start_date'], $timezone)->setTimezone('Asia/Jakarta');
        $endDate = Carbon::parse($data['end_date'], $timezone)->addDay()->setTimezone('Asia/Jakarta');
    
        $outletModels = [
            1 => SellingOutlet1::query(),
            2 => SellingOutlet2::query(),
            3 => SellingOutlet3::query(),
        ];
    
        $aboutModels = [
            1 => AboutOutlet1::query(),
            2 => AboutOutlet2::query(),
            3 => AboutOutlet3::query(),
        ];
    
        $about = $aboutModels[$data['outlet_id']]->first();
    
        if (isset($outletModels[$data['outlet_id']])) {
            $sellings = $outletModels[$data['outlet_id']]
                ->select()
                ->with(
                    'sellingDetails:id,selling_id,product_id,qty,price,cost,discount_price',
                    'sellingDetails.product:id,name,initial_price,selling_price,sku',
                    'paymentMethod:id,name'  // Eager load the paymentMethod relationship
                )
                ->when($startDate, function (Builder $query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $sellings = collect(); // Return an empty collection if `outlet_id` is not found
        }
    
        $header = [
            'shop_name' => $about?->shop_name,
            'shop_location' => $about?->shop_location,
            'business_type' => $about?->business_type,
            'owner_name' => $about?->owner_name,
            'start_date' => $startDate->setTimezone($timezone)->format('d F Y'),
            'end_date' => $endDate->subDay()->setTimezone($timezone)->format('d F Y'),
        ];
    
        $reports = [];
        $paymentMethodTotals = [];  // Store totals by payment method
    
        $totalQty = 0;
        $totalCost = 0;
        $totalGross = 0;
        $totalNet = 0;
        $totalGrossProfit = 0;
        $totalDiscount = 0;
        $totalDiscountPerItem = 0;
        $totalNetProfitBeforeDiscountSelling = 0;
        $totalNetProfitAfterDiscountSelling = 0;
    
        /** @var SellingOutlet1 $selling */
        foreach ($sellings as $selling) {
            $totalDiscountPerItem = 0;
            $totalBeforeDiscountPerSelling = 0;
            $totalAfterDiscountPerSelling = 0;
            $totalNetProfitPerSelling = 0;
            $totalGrossProfitPerSelling = 0;
            $totalCostPerSelling = 0;
            $totalQtyPerSelling = 0;
    
            /** @var SellingDetailOutlet1 $detail */
            foreach ($selling->sellingDetails as $detail) {
                $totalDiscountPerItem += ($detail->discount_price ?? 0);
                $totalBeforeDiscountPerSelling += $detail->price;
                $totalAfterDiscountPerSelling += ($detail->price - ($detail->discount_price ?? 0));
                $totalNetProfitPerSelling += (($detail->price - $detail->cost) - ($detail->discount_price ?? 0));
                $totalGrossProfitPerSelling += ($detail->price - $detail->cost);
                $totalCostPerSelling += $detail->cost;
                $totalQtyPerSelling += $detail->qty;
    
                // Store individual reports for each selling detail
                $reports[] = [
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
                    'payment_method' => $selling->paymentMethod ? $selling->paymentMethod->name : 'Unknown'
                ];
            }
    
            // Add totals for payment method
            $paymentMethod = $selling->paymentMethod ? $selling->paymentMethod->name : 'Unknown';
    
            if (!isset($paymentMethodTotals[$paymentMethod])) {
                $paymentMethodTotals[$paymentMethod] = [
                    'total_sales' => 0,
                    'total_with_tax' => 0,
                ];
            }
    
            $paymentMethodTotals[$paymentMethod]['total_sales'] += $totalBeforeDiscountPerSelling;
            $paymentMethodTotals[$paymentMethod]['total_with_tax'] += $totalAfterDiscountPerSelling;
    
            // Summing up total values
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
    
        // Footer with totals for payment methods
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
            'payment_method_totals' => $paymentMethodTotals,  // Adding total by payment method
        ];
    
        return [
            'header' => $header,
            'reports' => $reports,
            'footer' => $footer,
        ];
    }

    private function formatCurrency($value)
    {
        return Number::format($value);  // Format numbers according to your needs
    }
}

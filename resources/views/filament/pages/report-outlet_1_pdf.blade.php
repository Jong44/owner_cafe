<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; }
    </style>
</head>
<body>
    <div style="text-align: center;">
        <h1>Report Penjualan</h1>
        <h3>{{ $header['shop_name'] }}</h3>
        <p>Period: <strong>{{ $header['start_date'] }} - {{ $header['end_date'] }}</strong></p>
    </div>

    <!-- Tabel Data Penjualan -->
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Harga Jual</th>
                <th>Qty</th>
                <th>Penjualan</th>
                <th>Discount per Item</th>
                <th>Penjualan Setelah Discount</th>
                <th>Gross Profit</th>
                <th>Net Profit</th>
                <th>Metode Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @if ($reports)
                @foreach ($reports as $report)
                    @php
                        $result = json_decode(json_encode($report));
                    @endphp
               <tr>
    <td>{{ $result->sku }}</td>
    <td>{{ $result->name }}</td>
    <td>{{ $result->selling_price }}</td>
    <td>{{ $result->qty }}</td>
    <td>{{ $result->selling }}</td>
    <td>{{ $result->discount_price }}</td>
    <td>{{ $result->total_after_discount }}</td>
    <td>{{ $result->gross_profit }}</td>
    <td>{{ $result->net_profit }}</td>
    <td>{{ $result->payment_method }}</td>
</tr>


                @endforeach
                <!-- Baris Grand Total / Total Keseluruhan -->
                @php
                    $footer = json_decode(json_encode($footer));
                @endphp
               <tr>
    <td colspan="3" style="font-weight: bold;">Grand Total</td>
    <td style="font-weight: bold;">{{ number_format($footer->total_qty, 0, ',', '.') }}</td>
    <td style="font-weight: bold;">{{ 'Rp ' . $footer->total_gross }}</td>
    <td style="font-weight: bold;">{{ 'Rp ' . $footer->total_discount_per_item }}</td>
    <td style="font-weight: bold;">{{ 'Rp ' . $footer->total_net }}</td>
    <td style="font-weight: bold;">{{ 'Rp ' . $footer->total_gross_profit }}</td>
    <td style="font-weight: bold;">{{ 'Rp ' . $footer->total_net_profit_before_discount_selling }}</td>
    <td></td>
</tr>

            @else
                <tr>
                    <td colspan="10">
                        Belum ada penjualan untuk periode <strong>{{ $header['start_date'] }} - {{ $header['end_date'] }}</strong>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <br>

    <!-- Tabel Total Penjualan Berdasarkan Metode Pembayaran -->
    <table>
        <thead>
            <tr>
                <th colspan="3">Total Penjualan Berdasarkan Metode Pembayaran</th>
            </tr>
            <tr>
                <th>Metode Pembayaran</th>
                <th>Total Penjualan</th>
                <th>Total dengan Pajak</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($footer->payment_method_totals as $paymentMethod => $totals)
                <tr>
                    <td>{{ $paymentMethod }}</td>
                    <td>{{ 'Rp ' . number_format($totals->total_sales, 0, ',', '.') }}</td>
                    <td>{{ 'Rp ' . number_format($totals->total_with_tax, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

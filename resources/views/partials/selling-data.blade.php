<div>
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-semibold">{{ __('Selling Report') }}</h1>
        <h3 class="text-xl">{{ $header['shop_name'] }}</h3>
    </div>
    <p class="mb-4">{{ __('Period') }}: <b>{{ $header['start_date'] }} - {{ $header['end_date'] }}</b></p>

    <!-- Tabel Data Penjualan -->
    <table class="w-full text-left bg-white border border-gray-200 divide-y divide-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="p-4 text-center text-sm">SKU</th>
                <th class="p-4 text-center text-sm">Nama Produk</th>
                <th class="p-4 text-center text-sm">Harga Jual</th>
                <th class="p-4 text-center text-sm">Qty</th>
                <th class="p-4 text-center text-sm">Penjualan</th>
                <th class="p-4 text-center text-sm">Diskon per Item</th>
                <th class="p-4 text-center text-sm">Penjualan Setelah Diskon</th>
                <th class="p-4 text-center text-sm">Gross Profit</th>
                <th class="p-4 text-center text-sm">Net Profit</th>
                <th class="p-4 text-center text-sm">Metode Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @if ($reports)
                @foreach ($reports as $report)
                    @php
                        $result = json_decode(json_encode($report));
                    @endphp
                    <tr>
                        <td class="p-4 text-center text-sm">{{ $result->sku }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->name }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->selling_price }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->qty }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->selling }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->discount_price }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->total_after_discount }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->gross_profit }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->net_profit }}</td>
                        <td class="p-4 text-center text-sm">{{ $result->payment_method }}</td>
                    </tr>
                @endforeach

                <!-- Baris Total -->
                @php
                    $footer = json_decode(json_encode($footer));
                @endphp
                <tr class="border-t border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 font-bold">
                    <td class="p-4 text-center text-sm" colspan="3">Total</td>
                    <td class="p-4 text-center text-sm">{{ $footer->total_qty }}</td>
                    <td class="p-4 text-center text-sm">{{ $footer->total_gross }}</td>
                    <td class="p-4 text-center text-sm">{{ $footer->total_discount_per_item }}</td>
                    <td class="p-4 text-center text-sm">{{ $footer->total_net }}</td>
                    <td class="p-4 text-center text-sm">{{ $footer->total_gross_profit }}</td>
                    <td class="p-4 text-center text-sm">{{ $footer->total_net_profit_after_discount_selling }}</td>
                    <td class="p-4 text-center text-sm">-</td>
                </tr>

                <!-- Tambahan Baris: Diskon Transaksi -->
                <tr class="bg-gray-100 dark:bg-gray-700 font-semibold">
                    <td colspan="5" class="p-4 text-sm text-right">Diskon Transaksi</td>
                    <td colspan="5" class="p-4 text-left text-sm">{{ $footer->total_discount }}</td>
                </tr>
            @else
                <tr>
                    <td class="p-4 text-center text-base font-bold" colspan="10">
                        Belum ada penjualan {{ __('Period') }}: <b>{{ $header['start_date'] }} - {{ $header['end_date'] }}</b>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <br>

    <!-- Tabel Total Penjualan Berdasarkan Metode Pembayaran -->
    <table class="w-full text-left bg-white border border-gray-200 divide-y divide-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="p-4 text-center text-base border-b font-bold" colspan="3">Total Penjualan Berdasarkan Metode Pembayaran</th>
            </tr>
            <tr>
                <th class="p-4 text-center text-sm">Metode Pembayaran</th>
                <th class="p-4 text-center text-sm">Total Penjualan</th>
                <th class="p-4 text-center text-sm">Total dengan Pajak</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($footer->payment_method_totals as $paymentMethod => $totals)
                <tr>
                    <td class="p-4 text-center text-sm">{{ $paymentMethod }}</td>
                    <td class="p-4 text-center text-sm">{{ 'Rp ' . number_format($totals->total_sales, 0, ',', '.') }}</td>
                    <td class="p-4 text-center text-sm">{{ 'Rp ' . number_format($totals->total_with_tax, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

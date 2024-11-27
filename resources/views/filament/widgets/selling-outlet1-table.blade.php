<x-filament-widgets::widget>
    <h1 class="text-2xl font-semibold text-gray-900">Laporan Penjualan Hari Ini</h1>
    <br>
    <table class="w-full text-left bg-white border border-gray-200 divide-y divide-gray-200 rounded-xl">
        <thead class="bg-gray-50">
            <tr>
                <th class="p-4">SKU</th>
                <th class="p-4">Nama Produk</th>
                <th class="p-4">Harga Jual</th>
                <th class="p-4">Harga Beli</th>
                <th class="p-4">Qty</th>
                <th class="p-4">Diskon</th>
                <th class="p-4">Total Setelah Diskon</th>
                <th class="p-4">Total Penjualan</th>
                <th class="p-4">Total Laba</th>
                <th class="p-4">Total Laba Kotor</th>
            </tr>
        </thead>
        <tbody>
           @if ($reports['reports'])
                @foreach ($reports['reports'] as $report)
                    @php
                        $result = json_decode(json_encode($report));
                    @endphp
                    <tr>
                        <td class="p-4">{{ $result->sku }}</td>
                        <td class="p-4">{{ $result->name }}</td>
                        <td class="p-4">{{ $result->selling_price }}</td>
                        <td class="p-4">{{ $result->cost }}</td>
                        <td class="p-4">{{ $result->qty }}</td>
                        <td class="p-4">{{ $result->discount_price }}</td>
                        <td class="p-4">{{ $result->total_after_discount }}</td>
                        <td class="p-4">{{ $result->selling }}</td>
                        <td class="p-4">{{ $result->net_profit }}</td>
                        <td class="p-4">{{ $result->gross_profit }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="p-4" colspan="3">Belum ada penjualan hari ini</td>
                </tr>
            @endif
        </tbody>
    </table>
</x-filament-widgets::widget>

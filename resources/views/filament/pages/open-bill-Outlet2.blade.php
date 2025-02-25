<x-filament::page>
    <div class="w-full px-4 py-2">
        <!-- Header dan Search -->
        <div class="mb-4 flex flex-col sm:flex-row items-center justify-between">
            <h2 class="text-2xl font-bold mb-4 sm:mb-0">Bills for Outlet 2</h2>
            <div>
                <input
                    type="text"
                    wire:model.debounce.300ms="search"
                    placeholder="Cari berdasarkan nama..."
                    class="px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
        </div>

        <!-- Tabel Data Bills -->
        <div class="overflow-x-auto w-full bg-white shadow rounded-lg">
            <table class="table-auto w-full text-sm text-left text-gray-500">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">User ID</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tax</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Voucher</th>
                        <th class="px-4 py-3">Created At</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($this->bills as $index => $bill)
                        <tr>
                            <td class="px-4 py-2">
                                {{ $this->bills->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $bill->name }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $bill->user_id }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $bill->status }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $bill->tax }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $bill->total }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $bill->voucher }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $bill->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-2">
                                <button
                                    wire:click="deleteBill({{ $bill->id }})"
                                    class="inline-flex items-center text-white px-3 py-1 rounded hover:bg-red-700" style="background-color: #dc2626;"
                                    onclick="confirm('Anda yakin ingin menghapus bill ini?') || event.stopImmediatePropagation()"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 
                                               01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v0
                                               a2 2 0 01-2 2h-4a2 2 0 01-2-2v0
                                               a2 2 0 012-2z"
                                        />
                                    </svg>
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-2 text-center text-gray-500">
                                Tidak ada data bill untuk Outlet 1.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($this->bills->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $this->bills->links() }}
            </div>
        @endif
    </div>
</x-filament::page>

<?php

namespace App\Filament\Pages;

use App\Models\BillOutlet1;
use App\Models\BillOutlet3;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\WithPagination;

class BillOutlet_3 extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Outlet 3';
    protected static ?string $title = 'Open Bills';
    protected static string $view = 'filament.pages.open-bill-Outlet3';

    public $search = '';

    /**
     * Saat properti $search berubah, reset ke halaman 1.
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Membuat property Livewire "bills" yang akan ditampilkan di tabel.
     * Menggunakan pagination 10 data per halaman, serta filter pencarian
     * berdasarkan kolom 'name'.
     */
    public function getBillsProperty()
    {
        return BillOutlet3::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(10);
    }

    /**
     * Menghapus bill berdasarkan ID, lalu memunculkan notifikasi.
     */
    public function deleteBill($billId)
    {
        $bill = BillOutlet3::findOrFail($billId);
        $bill->delete();

        Notification::make()
            ->title('Bill berhasil dihapus.')
            ->success()
            ->send();

        $this->resetPage();
    }
}

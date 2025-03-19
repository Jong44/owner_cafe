<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    // Tentukan nama tabel jika tidak sesuai konvensi
    protected $table = 'payment_methods'; // Sesuaikan dengan nama tabel di database Anda

    protected $fillable = ['name', 'is_cash', 'is_debit', 'is_credit', 'is_wallet', 'icon', 'waletable_type', 'waletable_id'];

    // Relasi dengan Sellings (atau SellingOutlet1)
    public function sellings()
    {
        return $this->hasMany(SellingOutlet1::class, 'payment_method_id', 'id');
    }
}

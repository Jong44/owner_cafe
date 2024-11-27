<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SellingOutlet2 extends Model
{
    protected $connection = 'mysql_2';
    protected $table = 'sellings';

    protected $appends = [
        'grand_total_price',
    ];

    public function sellingDetails()
    {
        return $this->hasMany(SellingDetailOutlet2::class, 'selling_id', 'id');
    }

    public function scopeIsPaid(Builder $builder): Builder
    {
        return $builder->where('is_paid', true);
    }

    public function scopeIsNotPaid(Builder $builder): Builder
    {
        return $builder->where('is_paid', false);
    }

    public function grandTotalPrice(): Attribute
    {
        return Attribute::make(get: fn () => $this->total_price - $this->tax_price - $this->total_discount_per_item - $this->discount_price);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SellingOutlet3 extends Model
{
    protected $connection = 'mysql_3';
    protected $table = 'sellings';

    protected $appends = [
        'grand_total_price',
    ];

    public function scopeIsPaid(Builder $builder): Builder
    {
        return $builder->where('is_paid', true);
    }

    public function scopeIsNotPaid(Builder $builder): Builder
    {
        return $builder->where('is_paid', false);
    }
}
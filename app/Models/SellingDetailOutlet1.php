<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingDetailOutlet1 extends Model
{


    use HasFactory;
    protected $connection = 'mysql_1';
    protected $table = 'selling_details';

    protected $guarded = [];

    public function selling()
    {
        return $this->belongsTo(SellingOutlet1::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductOutlet1::class, 'product_id', 'id');
    }

    public function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price - $this->discount_price,
        );
    }
}

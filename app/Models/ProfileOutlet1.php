<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProfileOutlet1 extends Model
{
    protected $fillable = [
        'phone',
        'address',
        'locale',
        'photo',
        'timezone',
    ];


}

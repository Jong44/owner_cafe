<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillOutlet3 extends Model
{
    protected $connection = 'mysql_3';
    protected $table = 'bills';

    protected $guarded = ['id'];
}

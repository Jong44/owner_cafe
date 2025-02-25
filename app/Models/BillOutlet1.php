<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillOutlet1 extends Model
{
    protected $connection = 'mysql_1';
    protected $table = 'bills';

    protected $guarded = ['id'];
}

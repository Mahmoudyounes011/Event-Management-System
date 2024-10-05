<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Content extends Pivot
{
    use HasFactory;

    public $table = 'contents';

    public $primaryKey = 'id';

    protected $fillable = ['store_product_id','order_id','quantity'];

    protected $hidden = ['updated_at','created_at','order_id'];

}

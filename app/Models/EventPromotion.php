<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;

class eventpromotion extends MorphPivot
{
    use HasFactory;

    public $table = 'event_promotions';

    protected $fillable = ['eventable_id','eventable_type','end_at'];

}

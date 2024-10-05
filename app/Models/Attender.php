<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Attender extends MorphPivot
{
    use HasFactory;

    public $table = 'attenders';

    public $primaryKey = 'id';

    protected $fillable = ['user_id','eventable_id','eventable_type','created_at'];

    protected $hidden = ['user_id','eventable_id','eventable_type'];

}

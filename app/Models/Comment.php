<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Comment extends MorphPivot
{
    use HasFactory;

    public $table = 'comments';

    protected $fillable = ['user_id','eventable_id','eventable_type','comment','created_at'];

    protected $hidden = ['user_id','eventable_id','eventable_type'];

}

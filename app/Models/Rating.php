<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Rating extends Pivot
{
    use HasFactory;

    public $table = 'ratings';

    protected $fillable = [
        'user_id',
        'rateable_id',
        'rateable_type',
        'stars',
    ];

    // public function rateable()
    // {
    //     return $this->morphTo();
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

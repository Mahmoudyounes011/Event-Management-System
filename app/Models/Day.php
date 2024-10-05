<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;
    protected $fillable = ['day'];
    protected $hidden = ['id','created_at','updated_at'];

    // public function venues()
    // {
    //     return $this->morphedByMany(Venue::class, 'schedulable');
    // }

    // public function stores()
    // {
    //     return $this->morphedByMany(Store::class, 'schedulable');
    // }
}

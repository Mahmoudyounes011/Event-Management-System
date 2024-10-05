<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function venues()
    {
        return $this->belongsToMany(Venue::class,'venue_owners','owner_id','venue_id');
    }
}

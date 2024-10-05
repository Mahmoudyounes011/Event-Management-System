<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capacity extends Model
{
    use HasFactory;

    public $fillable  = ['capacity'];

    protected $hidden = ['public_event_id','created_at','updated_at'];

}

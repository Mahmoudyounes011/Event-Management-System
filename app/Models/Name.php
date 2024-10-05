<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    use HasFactory;

    public $table = 'names';

    protected $fillable = ['name'];

    protected $hidden = ['created_at','updated_at'];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costumlocation extends Model
{
    use HasFactory;

    public $table = 'costumlocations';

    protected $fillable = ['order_id','longitude','latitude'];

    protected $hidden = ['created_at','updated_at'];

}

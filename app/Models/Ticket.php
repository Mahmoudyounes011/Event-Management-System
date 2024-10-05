<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public $fillable = ['ticketable_type','ticketable_id','price'];

    protected $hidden = ['ticketable_type','ticketable_id','created_at','updated_at'];

}

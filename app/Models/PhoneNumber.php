<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    use HasFactory;

    public $table = 'phone_numbers';

    protected $fillable = ['phone_number','phoneable_id','phoneable_type'];

    protected $hidden =['phoneable_id','phoneable_type','created_at','updated_at'];

    public function relatedTo()
    {
        return $this->morphTo('phoneable')->withoutGlobalScope('available');
    }
}

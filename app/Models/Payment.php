<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Payment extends Pivot
{
    use HasFactory;

    public $table = 'payments';

    protected $fillable = ['amount','date'];

    public function payer()
    {
        return $this->belongsTo(User::class,'payer_id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class,'payee_id');
    }

}

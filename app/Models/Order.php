<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','store_id','hasDelivery','date','time','status'];

    protected $hidden = ['updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function products()
    {
        return $this->belongsToMany(StoreProduct::class,'contents')->using(Content::class)->withPivot(['quantity']);
    }

    public function location()
    {
        return $this->belongsToMany(Venue::class,'locations');
    }

    public function costum_location()
    {
        return $this->hasOne(Costumlocation::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}

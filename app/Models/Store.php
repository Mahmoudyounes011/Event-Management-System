<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable =['user_id','name','description','longitude','latitude','hasDelivery','deliveryCost'];

    protected $hidden =['available','created_at','updated_at'];

    public $table = 'stores';

    protected static function booted()
    {
        static::addGlobalScope('available', function (Builder $builder) {
            $builder->where('available',1);
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function times()
    {
        return $this->morphToMany(Day::class,'schedulable','day_venues')->using(FreeTime::class)->withPivot(['id','start_time','end_time'])->orderBy('day')->orderByPivot('start_time');
    }

    public function photos()
    {
        return $this->morphMany(Image::class,'imagable');
    }

    public function phones()
    {
        return $this->morphMany(PhoneNumber::class,'phoneable');
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class,'store_products')->using(StoreProduct::class)->withPivot(['price']);
    // }

    public function products()
    {
        return $this->hasMany(StoreProduct::class)->where('available',1);
    }

    public function rate_users()
    {
        return $this->morphToMany(User::class,'ratable','ratings');
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->where('status','accepted')->with('products.product','user','location','costum_location');
    }

    public function requests()
    {
        return $this->hasMany(Order::class)->where('status','pending')->with('products.product','location','costum_location');
    }

    public function rejects()
    {
        return $this->hasMany(Order::class)->where('status','rejected')->with('products.product','location','costum_location');
    }

    public function times_of_day($day)
    {
        return $this->times()->orderByPivot('start_time')->where('day',$day);
    }

    public function ratings()
    {
        return $this->morphToMany(User::class, 'rateable','ratings')->withPivot(['stars']);
    }

    public function wallet()
    {
        return $this->morphOne(Wallet::class,'walletable');
    }
}

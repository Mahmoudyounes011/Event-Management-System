<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Venue extends Model
{
    use HasFactory;

    public $table = 'venues';

    protected $fillable = ['user_id','name','description','longitude','latitude','available'];

    protected $hidden =['available','created_at','updated_at'];

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

    public function photos()
    {
        return $this->morphMany(Image::class,'imagable');
    }

    public function phones()
    {
        return $this->morphMany(PhoneNumber::class,'phoneable');
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->with('name');
    }

    public function times()
    {
        return $this->morphToMany(Day::class,'schedulable','day_venues')->using(FreeTime::class)->withPivot(['id','start_time','end_time'])->orderBy('day')->orderByPivot('start_time');
    }


    public function times_of_day($day)
    {
        return $this->times()->orderByPivot('start_time')->where('day',$day);
    }

    public function owners()
    {
        return $this->belongsToMany(Owner::class,'venue_owners','venue_id','owner_id');
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

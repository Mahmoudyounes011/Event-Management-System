<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use Illuminate\Database\Eloquent\Builder;
use Monolog\Level;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['section_id','description','name_id','capacity','price','available'];

    protected $hidden =['created_at','updated_at','available','name_id'];

    protected static function booted()
    {
        static::addGlobalScope('available', function (Builder $builder) {
            $builder->where('available',1);
        });
    }

    public function rate_users()
    {
        return $this->morphToMany(User::class,'ratable','ratings');
    }


    public function events_of_date($date)
    {
        return $this->belongsToMany(User::class,'events')->wherePivot('date',$date)->withPivot(['start_time','end_time'])->orderByPivot('start_time');
    }

    // public function events()
    // {
    //     return $this->belongsToMany(User::class,'events')->withPivot(['name','description','capacity','date','start_time','period','privacy']);
    // }

    public function events()
    {
        return $this->hasMany(Event::class)->where('status','accepted')->with('photos');
    }

    public function requests()
    {
        return $this->hasMany(Event::class)->where('status','pending')->with('photos');
    }

    public function rejects()
    {
        return $this->hasMany(Event::class)->where('status','rejected')->with('photos');
    }

    public function photos()
    {
        return $this->morphMany(Image::class,'imagable');
    }

    public function users_events()
    {
        return $this->belongsToMany(User::class,'events');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'section_categories');
    }

    public function categories_pivot()
    {
        return $this->hasMany(SectionCategory::class)->where('available',1);
    }

    // public function levels($category_id)
    // {
    //     return $this->hasManyThrough(SectionLevel::class,SectionCategory::class,'section_id','section_category_id')
    //     ->whereHas('section_categories',function($query) use ($category_id)
    //     {
    //         $query->where('category_id',$category_id);
    //     });
    // }
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function name()
    {
        return $this->belongsTo(Name::class);
    }


}

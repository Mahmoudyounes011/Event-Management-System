<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicEvent extends Model
{
    use HasFactory;

    public $fillable = ['name','description','latitude','longitude','start_time','end_time','date','privacy'];

    protected $hidden = ['created_at','updated_at'];


    public function ticket()
    {
        return $this->morphOne(Ticket::class,'ticketable');
    }

    public function capacity()
    {
        return $this->hasOne(Capacity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->morphMany(Image::class,'imagable');
    }

    public function promotions()
    {
        return $this->morphToMany(Promotion::class,'eventable','event_promotions')->withPivot(['hours']);
    }

    public function comments()
    {
        return $this->morphToMany(User::class, 'eventable','comments')->using(Comment::class)->withPivot(['comment','created_at']);
    }

    public function attenders()
    {
        return $this->morphToMany(User::class, 'eventable','attenders')->using(Attender::class)->withPivot(['created_at']);
    }

    public function attenderPivot()
    {
        return $this->morphMany(Attender::class,'eventable');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Event extends Model
{
    use HasFactory;

    public $table = 'events';

    public $primaryKey = 'id';

    protected $fillable = ['user_id','section_id','status','name','description','capacity','date','start_time','end_time','privacy'];
    protected $hidden = ['created_at','updated_at'];

    public function pivot()
    {
        return $this->hasOne(EventLevel::class);
    }

    public function photos()
    {
        return $this->morphMany(Image::class,'imagable');
    }

    public function ticket()
    {
        return $this->morphOne(Ticket::class,'ticketable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class)->with('venue.owner','venue.ratings','name');
    }

    public function promotions()
    {
        return $this->morphToMany(Promotion::class,'eventable','event_promotions')->withPivot(['end_at']);
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

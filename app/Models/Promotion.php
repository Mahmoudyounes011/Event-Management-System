<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['type','description','cost'];

    protected $hidden = ['updated_at','created_at'];

    public function events()
    {
        return $this->morphedByMany(Event::class, 'eventable');
    }

    public function publicEvents()
    {
        return $this->morphedByMany(PublicEvent::class, 'eventable');
    }


}

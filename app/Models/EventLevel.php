<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventLevel extends Model
{
    use HasFactory;

    public $table = 'event_levels';

    public $fillable = ['event_id','level_id'];

    protected $hidden = ['event_id','level_id','created_at','updated_at'];


    public $primaryKey = 'id';

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}

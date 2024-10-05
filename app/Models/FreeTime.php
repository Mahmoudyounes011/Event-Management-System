<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class FreeTime extends MorphPivot
{
    use HasFactory;

    public $table = 'day_venues';

    protected $hidden = ['schedulable_id','schedulable_type','day_id'];

    protected $fillable = ['day_id','shcedulable_type','shcedulable_id','start_time','end_time'];

    public function relatedTo()
    {
        return $this->morphTo('schedulable')->withoutGlobalScope('available');
    }
}

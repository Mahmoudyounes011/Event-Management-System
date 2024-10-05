<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public $table = 'images';

    protected $fillable =['path','imagable_id','imagable_type'];

    protected $hidden =['imagable_id','imagable_type','created_at','updated_at'];

    public function relatedTo()
    {
        return $this->morphTo('imagable')->withoutGlobalScope('available');
    }
}

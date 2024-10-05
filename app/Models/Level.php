<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    public $table = 'levels';

    public $primaryKey = 'id';

    protected $fillable = ['section_category_id','level','price','available'];

    protected $hidden = ['section_category_id','available','created_at','updated_at'];

    protected static function booted()
    {
        static::addGlobalScope('available', function (Builder $builder) {
            $builder->where('available',1);
        });
    }

    public function category()
    {
        return $this->belongsTo(SectionCategory::class,'section_category_id');
    }

}

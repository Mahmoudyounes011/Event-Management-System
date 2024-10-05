<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SectionCategory extends Model
{
    use HasFactory;

    public $table = 'section_categories';

    public $primaryKey = 'id';

    protected $fillable = ['category_id','section_id','available'];

    protected $hidden = ['created_at','updated_at','category_id','section_id','available'];

    protected static function booted()
    {
        static::addGlobalScope('available', function (Builder $builder) {
            $builder->where('available',1);
        });
    }

    public function levels()
    {
        return $this->hasMany(Level::class)->where('available',1);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class)->with('name');
    }
}

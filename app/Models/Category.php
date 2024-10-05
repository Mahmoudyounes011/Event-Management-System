<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $hidden =['created_at','updated_at'];


    public function section_category()
    {
        return $this->hasMany(SectionCategory::class)->with('levels','section.photos','section.venue.phones','section.venue.photos');
    }
}

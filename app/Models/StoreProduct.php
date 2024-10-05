<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StoreProduct extends Model
{
    use HasFactory;

    public $table = 'store_products';

    public $primaryKey = 'id';


    protected $fillable = ['store_id','product_id','price','available'];

    protected $hidden = ['store_id','product_id','updated_at','created_at','available'];

    protected static function booted()
    {
        static::addGlobalScope('available', function (Builder $builder) {
            $builder->where('available',1);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->with('photos');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

}

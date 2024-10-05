<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public $table = 'roles';
    public $primaryKey = 'id';
    protected $fillable = ['role'];

    public function venues()
    {
        return $this->belongsToMany(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'user_roles')->using(UserRole::class);
    }

}

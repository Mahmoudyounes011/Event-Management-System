<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $table = 'users';
    public $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'email',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];



    public function isAdmin()
    {
        $roles = $this->roles;

        foreach($roles as $role)
        {

            if ($role->role === "admin")
                return true;
        }

        return false;
    }

    public function isOwner()
    {
        $roles = $this->roles;

        foreach($roles as $role)
        {
            if ($role->role === "owner")
                return true;
        }

        return false;
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function payments()
    {
        return $this->belongsToMany(User::class, 'payments', 'payer_id', 'payee_id')
        ->using(Payment::class)->withPivot(['amount','date']);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class,'user_roles')->using(UserRole::class);
    }

    public function rate_venues($venue_id)
    {
        return $this->hasMany(Rating::class)->where('rateable_id',$venue_id)->where('rateable_type','App\\Models\\Venue');
    }

    public function rate_stores($store_id)
    {
        return $this->hasMany(Rating::class)->where('rateable_id',$store_id)->where('rateable_type','App\\Models\\Store');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function comments()
    {
        return $this->morphedByMany(Event::class, 'eventable','comments')->withPivot(['comment','created_at']);
    }

    public function public_comments()
    {
        return $this->morphedByMany(PublicEvent::class, 'eventable','comments')->withPivot(['comment','created_at']);
    }

    public function public_events()
    {
        return $this->hasMany(PublicEvent::class)->with('photos');
    }

    public function check_if_registered($event_id)
    {
        return $this->morphedByMany(Event::class, 'eventable','attenders')->where('events.id',$event_id);
    }

    public function public_check_if_registered($event_id)
    {
        return $this->morphedByMany(PublicEvent::class, 'eventable','attenders')->where('public_events.id',$event_id);
    }

    public function registers()
    {
        return $this->morphedByMany(Event::class, 'eventable','attenders')->withPivot(['created_at']);
    }

    public function public_registers()
    {
        return $this->morphedByMany(PublicEvent::class, 'eventable','attenders')->withPivot(['created_at']);
    }

    public function events()
    {
        return $this->hasMany(Event::class)->with('photos');
    }
    public function orders()
    {
        return $this->hasMany(Order::class)->with('products.product','location','costum_location');
    }

    public function wallet()
    {
        return $this->morphOne(Wallet::class,'walletable');
    }




}

<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class PackagesShare extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','package_id', 'created_at', 'shared_via', 'receiver_name', 'contact_info', 'coupon_generated',
    ];

    protected $connection = 'likes_shares';
    protected $table = 'packages_share';


    // Get user details
    public function getUserDetails(){
        return $this->hasMany(UserDetails::class, 'id','user_id')->select('id','first_name','last_name','email','mobile');
    }
}

<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Cart extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','status',
    ];

    protected $connection = 'cart_management';
    protected $table = 'cart';
    
    // Get User details
    public function getUserDetails(){
        return $this->hasOne(UserDetails::class, 'id','user_id');
    }

    // Get User details
    public function getPackageDetails(){
        return $this->hasOne(CartPackage::class, 'cartID','id');
    }
}

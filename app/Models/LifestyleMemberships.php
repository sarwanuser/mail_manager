<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class LifestyleMemberships extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','membership_type','discount_percent','discount_code','effective_date','expiry_date','renewal_date','is_active','created_at','updated_at',
    ];

    protected $connection = 'clykk_lifestyle';
    protected $table = 'lifestyle_membership';

    // Get user details
    public function getUser(){
        return $this->hasOne(UserDetails::class, 'id','user_id');
    }
    
    
}

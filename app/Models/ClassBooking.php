<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class ClassBooking extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'session_id', 'user_id', 'status', 'created_at', 'updated_at',
    ];

    protected $connection = 'clykk_lifestyle';
    protected $table = 'class_booking';

    // Get user details
    public function getSP(){
        return $this->hasOne(SPDetails::class, 'id','sp_id');
    }

    // Get user details
    public function getSession(){
        return $this->hasOne(ClassSession::class, 'id','session_id')->with('getPackage')->with('getSP');
    }

    // Get user details
    public function getUser(){
        return $this->hasOne(UserDetails::class, 'id','user_id');
    }
    
    
    
}

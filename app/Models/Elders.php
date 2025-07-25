<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Elders extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'elder_id','user_id','first_name','last_name','gender_id','date_of_birth','primary_phone_number','photo_url','created_at','updated_atac',
    ];

    protected $connection = 'clykk_24x7';
    protected $table = 'elders';

    // Get user details
    public function getUser(){
        return $this->hasOne(UserDetails::class, 'id','user_id');
    }
    
    
}

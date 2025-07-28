<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class ElderSubs extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','elder_id','user_id','residence_id','subscription_package_id','subscription_package_name','number_of_bedrooms','plan_activated_at','plan_expiry_at','plan_renewal_at','primary_caregiver_id','primary_caregiver_name','caregiver_relationship_type_id','caregiver_relationship_name','monitoring_enabled','created_at','updated_at',
    ];

    protected $connection = 'clykk_24x7';
    protected $table = 'elder_subscription';

    // Get user details
    public function getUser(){
        return $this->hasOne(UserDetails::class, 'id','user_id');
    }

    // Get elder details
    public function getElder(){
        return $this->hasOne(Elders::class, 'elder_id','elder_id');
    }
    
    
}

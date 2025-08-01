<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class ElderFamily extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'family_member_id','elder_id','user_id','relationship_type_id','communication_channel_id','access_level_id','is_active','is_primary_caregiver','created_at','updated_at','primary_elder_idac',
    ];

    protected $connection = 'clykk_24x7';
    protected $table = 'family_members';

    // Get user details
    public function getUser(){
        return $this->hasOne(UserDetails::class, 'id','user_id');
    }

    // Get elder details
    public function getElder(){
        return $this->hasOne(Elders::class, 'elder_id','elder_id');
    }

    // Get relationship type
    public function getRelationType(){
        return $this->hasOne(RelationshipType::class, 'id','relationship_type_id');
    }

    // Get communication channels details
    public function getCommChannel(){
        return $this->hasOne(CommunicationChannel::class, 'id','communication_channel_id');
    }

    // Get communication channels details
    public function getAccessLevel(){
        return $this->hasOne(AccessLevel::class, 'id','access_level_id');
    }
    
    
}

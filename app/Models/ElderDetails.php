<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class ElderDetails extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'elder_id','el_userid','first_name','last_name','gender_id','date_of_birth','primary_phone_number','photo_url','gender_code','family_member_id','user_id','relationship_type_id','communication_channel_id','access_level_id','is_active','is_primary_caregiver','primary_elder_id','userfname','userlname','useremail','usermobile','relationship_name','channel_name','access_level_name',
    ];

    protected $connection = 'clykk_24x7';
    protected $table = 'elder_details';
    
    
}

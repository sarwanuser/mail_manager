<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class ClassSession extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id', 'class_date', 'class_time', 'sp_id', 'join_url', 'status', 'sub_category_id', 'package_name', 'package_image_url', 'created_at', 'updated_at',
    ];

    protected $connection = 'clykk_lifestyle';
    protected $table = 'class_session';

    // Get user details
    public function getSP(){
        return $this->hasOne(SPDetails::class, 'id','sp_id');
    }
    
    
}

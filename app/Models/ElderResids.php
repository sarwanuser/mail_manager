<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class ElderResids extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'residence_id','elder_id','home_id','address_line1','address_line2','city','state','postal_code','country','photo_url','created_at','updated_at',
    ];

    protected $connection = 'clykk_24x7';
    protected $table = 'residences';

    // Get elder details
    public function getElder(){
        return $this->hasOne(Elders::class, 'elder_id','elder_id');
    }
    
    
}

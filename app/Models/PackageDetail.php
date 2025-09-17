<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class PackageDetail extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_at','created_by','updated_at','updated_by','enabled','sub_category_id','package_name','short_description','package_image','base_price','selling_price','display_order','rating','likes','sub_category_service_rule_id','sub_org_default','offer','offer_url','tags','city_id','city_namea'
    ];

    protected $connection = 'catalog_management';
    protected $table = 'package_detail';

    
}
<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class CartPackage extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cartID','package_name','short_description','package_image','base_price','selling_price','sub_category_id','service_rule_id',
    ];

    protected $connection = 'cart_management';
    protected $table = 'cart_package';

    // Get BookingOrder details
    public function getorderdetails(){
        return $this->hasOne(BookingOrder::class, 'cart_id','cartID');
    }

    // Get Transactions details
    public function getSubCategoryDetails(){
        return $this->hasOne(SubCategory::class, 'id','sub_category_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class BookingOrder extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','subscription_id','total','discount','discount_type','discount_code','payable_amount','amount_before_tax','tax_amount','payment_type','is_verified','collection_status','created_at','updated_at','order_id','cart_id','user_id','description',
    ];

    protected $connection = 'cart_management';
    protected $table = 'booking_order';

    // Get Cart Addon Package details
    public function getSubscriptions(){
        return $this->hasMany(Subscription::class, 'cart_id','cart_id')->with('cartData')->with('getQnaDetails')->with('serviceAddress')->with('deliveryAddress')->with('schedule')->with('getCartPackageDetails')->with('getAddonPackageDetails')->with('getSubTransactions');
    }
}

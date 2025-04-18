<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Subscription extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id','status','service_time','service_date','resched_count','sp_pay_cal',
    ];

    protected $connection = 'cart_management';
    protected $table = 'subscription';

    // Get CartPackage details
    public function getCartPackageDetails(){
        return $this->hasMany(CartPackage::class, 'cartID','cart_id')->with('getorderdetails')->with('getSubCategoryDetails');
    }

    // Get Cart Addon Package details
    public function getCartAddonPackageDetails(){
        return $this->hasMany(CartAddonPackage::class, 'cartID','cart_id');
    }

    // Get Cart Addon Package details
    public function getAddonPackageDetails(){
        return $this->hasMany(CartAddonPackage::class, 'cartID','cart_id')->where('item_count','>','0');
    }


    // Get cart details
    public function getcartdetails(){
        return $this->hasOne(Cart::class, 'id','cart_id')->with('getUserDetails');
    }

    // Get cart details
    public function cartData(){
        return $this->hasOne(Cart::class, 'id','cartID')->with('getUserDetails');
    }

    // Get customer qna details
    public function getQnaDetails(){
        return $this->hasMany(CartQna::class, 'cartID','cartID');
    }

    // Get Address details
    public function getAddressDetails(){
        return $this->hasMany(Address::class, 'cartID','cart_id');
    }

    // Get service Address details
    public function serviceAddress(){
        return $this->hasMany(Address::class, 'cartID','cartID')->where('addressType','service_address');
    }

    // Get delivery Address details
    public function deliveryAddress(){
        return $this->hasMany(Address::class, 'cartID','cartID')->where('addressType','delivery_address');
    }

    // Get delivery Address details
    public function schedule(){
        return $this->hasOne(Schedule::class, 'cartID','cartID');
    }

    // Get SP details
    public function getSPDetails(){
        return $this->hasOne(SPDetails::class, 'id','accept_provider_id');
    }

    // Get routing details
    public function getRoutingDetails(){
        return $this->hasOne(SPRoutingAlert::class, 'id','subscription_id')->with('getSPDetails');
    }

    // Get sp-payment details
    public function getSPPayments(){
        return $this->hasMany(SPPayment::class, 'subscription_id','subscription_id')->with('getPaymentTransaction');
    }

    // Get sp rating
    public function getRating(){
        return $this->hasOne(SPServiceRating::class, 'subscription_id','id');
    }

    // Get subscription transaction details
    public function getSubTransactions(){
        return $this->hasMany(SPSubscriptionTransaction::class, 'subscription_id','subscription_id');
    }

    // Get Transactions details
    public function getTransactionDetails(){
        return $this->hasOne(Transactions::class, 'subscription_id','id');
    }

    // Get SPTransaction details
    public function getSPTransactionDetails(){
        return $this->hasOne(SPTransaction::class, 'subscription_id','id');
    }

    

    

    
    
    
}

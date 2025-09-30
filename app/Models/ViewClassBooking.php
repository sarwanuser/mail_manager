<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class ViewClassBooking extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id','booking_status','booked_at','booking_updated_at','user_id','user_name','email','mobile','gender','lifestyle_membership_id','is_elder_enabled','session_id','package_id','sub_category_id','sp_id','class_date','class_time','session_start_at','join_url','session_status','pkg_name','display_package_name','display_package_image','pkg_short_description','base_price','selling_price','rating','likes','offer','offer_url','tags','pkg_city_id','pkg_city_name','pkg_enabled','sp_name','sp_mobile'
    ];

    protected $connection = 'clykk_lifestyle';
    protected $table = 'vw_user_class_bookings';

    
    
}

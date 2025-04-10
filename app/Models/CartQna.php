<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class CartQna extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cartID','question_type','question_name','question_sort_order','question_answer_type','question_answer','sort_order','customer_answer',
    ];

    protected $connection = 'cart_management';
    protected $table = 'cart_qna';
    
}

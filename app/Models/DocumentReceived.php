<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class DocumentReceived extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'referer','referer_id','document_id','document_url','status','comment','created_at','updated_at',
    ];

    protected $connection = 'sp_management';
    protected $table = 'document_received'; 

    // Get document details
    public function documentStatus(){
        return $this->hasOne(DocumentByCategory::class, 'document_id','id');
    }


    
}
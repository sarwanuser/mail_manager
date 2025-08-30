<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use  App\Models\Subscription;
use  App\Models\SubscriptionDatas;
use  App\Models\Routing;
use  App\Models\CartPackage;
use  App\Models\Cart;
use  App\Models\City;
use  App\Models\SPServiceSettings;
use  App\Models\SPRoutingAlert;
use  App\Models\SPRoutingAlertDetail;
use  App\Models\UserAlert;
use  App\Models\SPAlert;
use  App\Models\SPPayment;
use  App\Models\SPPaymentTransaction;
use  App\Models\SPPaymentHistory;
use  App\Models\SubCategoryServiceRule;
use  App\Models\SPDetails;
use  App\Models\Config;
use  App\Models\PackagesViewed;
use  App\Models\PackagesShare;
use  App\Models\SPTransaction;
use  App\Models\SPServiceRating;
use  App\Models\BookingOrder;
use  App\Models\Category;
use  App\Models\Address;
use  App\Models\LifestyleMemberships;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mail;
use Validator;
use DB;

class LifestyleMembershipController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * This function use for send invoice to customer
     *
     * @return Response
     */
    public function getAllLifestyleMemberships(Request $request){ 
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                $sub_id = $request->sub_id;
                

                $datas = LifestyleMemberships::orderBy('id', 'DESC')->with('getUser')->get();
                
                return response()->json(['status' => 1,'message' => 'List of Lifestyle Memberships', 'data' => $datas], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * This function use for send invoice to customer
     *
     * @return Response
     */
    public function getAllClassSessions(Request $request){ 
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                

                $datas = ClassSession::orderBy('id', 'DESC')->with('getSP')->get();
                
                return response()->json(['status' => 1,'message' => 'List of Class Sessions', 'data' => $datas], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }
}

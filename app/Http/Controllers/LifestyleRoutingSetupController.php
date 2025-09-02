<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
use  App\Models\Elders;
use  App\Models\ElderSubs;
use  App\Models\LifestyleRoutingSetup;
use Carbon\Carbon;
use Mail;
use Validator;
use DB;


class LifestyleRoutingSetupController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        try {
            $elder_id = $request->elder_id;
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                

                $datas = LifestyleRoutingSetup::orderBy('created_at', 'DESC')->get();
                
                return response()->json(['status' => 1,'message' => 'List of Lifestyle Routing Setup', 'data' => $datas], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [ 
            'lifestyle_sessions_id' => 'required',
            'rule_id' => 'required',
            'route_before' => 'required',
            'service_duration' => 'required',
            'open_time' => 'required',
            'close_time' => 'required',
            'min_commission' => 'required',
            'max_commission' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $elder_id = $request->elder_id;
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                $LifestyleRoutingSetup = new LifestyleRoutingSetup();
                $LifestyleRoutingSetup->lifestyle_sessions_id = $request->lifestyle_sessions_id;
                $LifestyleRoutingSetup->rule_id = $request->rule_id;
                $LifestyleRoutingSetup->route_before = $request->route_before;
                $LifestyleRoutingSetup->service_duration = $request->service_duration;
                $LifestyleRoutingSetup->open_time = $request->open_time;
                $LifestyleRoutingSetup->close_time = $request->close_time;
                $LifestyleRoutingSetup->min_commission = $request->min_commission;
                $LifestyleRoutingSetup->max_commission = $request->max_commission;
                $LifestyleRoutingSetup->created_at = date('Y-m-d, H:i:s');
                $LifestyleRoutingSetup->save(); 
                
                return response()->json(['status' => 1,'message' => 'Lifestyle Routing Setup Save Successfull!', 'data' => $LifestyleRoutingSetup], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'home_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $home_id = $request->home_id;
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                
                $residences = ElderResids::find($id);
                $residences->home_id = $home_id;
                $residences->save();
                
                return response()->json(['status' => 1,'message' => 'Home id updated for Elder Residences', 'data' => $residences], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

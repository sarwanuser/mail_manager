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
use  App\Models\ElderResids;
use Carbon\Carbon;
use Mail;
use Validator;
use DB;


class ElderResidences extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $validator = Validator::make($request->all(), [ 
            'elder_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $elder_id = $request->elder_id;
            // $AuthController = new AuthController();
            // $token_status = $AuthController->tokenVerify($request);
            // if($token_status['status'] == '200'){
                

                $datas = ElderResids::select('residence_id','elder_id','home_id','address_line1','address_line2','city','state','postal_code','country','photo_url','created_at','updated_at')->where('elder_id', $elder_id)->orderBy('created_at', 'DESC')->get();
                
                return response()->json(['status' => 1,'message' => 'List of Elder Residences', 'data' => $datas], 200);
                
            // }else{
            //      return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            // }

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
    public function store(Request $request)
    {
        //
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
            // $AuthController = new AuthController();
            // $token_status = $AuthController->tokenVerify($request);
            // if($token_status['status'] == '200'){
                
                $residences = ElderResids::find($id);
                $residences->home_id = $home_id;
                $residences->save();
                
                return response()->json(['status' => 1,'message' => 'Home id updated for Elder Residences', 'data' => $residences], 200);
                
            // }else{
            //      return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            // }

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

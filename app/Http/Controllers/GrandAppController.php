<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mail;
use Validator;
use DB;

class GrandAppController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * This function use for get the grandapp passcode by email.
     *
     * @return Response
     */
    public function getPassCodeByEmail(Request $request){
        $validator = Validator::make($request->all(), [ 
            'email' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://inclykkqa.grand-app.com/externalApiServlet',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{"ReceivePasscodeByClientId": {"phoneNumber":"'.$request->email.'"}}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return response()->json(['status' => 1,'message' => 'Grand App PassCode', 'passcode' => json_decode($response)->passcode], 200);                
           

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    } 



    /**
     * This function use for create new senior in grandapp
     *
     * @return Response
     */
    public function createNewSenior(Request $request){ 
        $request_data = json_encode($request->all());
        try {
            // $AuthController = new AuthController();
            // $token_status = $AuthController->tokenVerify($request);
            // if($token_status['status'] == '200'){
                
                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://inclykkqa.grand-app.com/externalApiServlet',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $request_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);


                return response()->json(['status' => 1,'message' => 'New Senior Created In Grand App', 'data' => json_decode($response)], 200);
                
            // }else{
            //      return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            // }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }
    
}

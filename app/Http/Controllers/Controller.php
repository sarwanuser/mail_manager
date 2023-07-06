<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use  App\Models\Subscription;
use  App\Models\Routing;
use  App\Models\CartPackage;
use  App\Models\Cart;
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
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mail;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * This function use for send invoice to customer
     *
     * @return Response
     */
    public function sendInvoiceToCustomer(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sub_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            //if($token_status['status'] == '200'){
                $sub_id = $request->sub_id;
                

                $data = Subscription::where('subscription.id', $sub_id)->where('subscription.status', 'Completed')->join('transaction','subscription.id','=','transaction.subscription_id')->with('getcartdetails')->with('getCartAddonPackageDetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getRoutingDetails')->orderBy('subscription.id', 'DESC')->get();
                $data = $data[0];
                $send_view = ["data" => $data];
                $cust = ["email" => $data->getcartdetails->getUserDetails->email, "name" => $data->getcartdetails->getUserDetails->first_name];
                
                Mail::send('invoice',$send_view,function($message) use ($cust){
                    $message->to('Keerthi.kumar@clykk.com');
                    $message->subject('Subscription Invoice');
                });
                //return View('invoice', compact('data'));
                return response()->json(['status' => 1,'message' => 'Subscription Invoice Sent', 'data' => []], 200);
                
            // }else{
            //      return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            // }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }
    }
}

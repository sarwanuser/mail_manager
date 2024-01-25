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
use  App\Models\Config;
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
            //$AuthController = new AuthController();
            //$token_status = $AuthController->tokenVerify($request);
            //if($token_status['status'] == '200'){
                $sub_id = $request->sub_id;
                

                $data = Subscription::where('subscription.id', $sub_id)->where('subscription.status', 'Completed')->join('transaction','subscription.id','=','transaction.subscription_id')->with('getcartdetails')->with('getCartAddonPackageDetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getRoutingDetails')->orderBy('subscription.id', 'DESC')->get();
                $data = $data[0];
                $send_view = ["data" => $data];
                $cust = ["email" => $data->getcartdetails->getUserDetails->email, "name" => $data->getcartdetails->getUserDetails->first_name];
                
                Mail::send('invoice',$send_view,function($message) use ($cust){
                    $message->to('Keerthi.kumar@clykk.com');
                    //$message->to('sarwanmawai@gmail.com');
                    $message->to($cust['email']);
                    $message->subject('Subscription Invoice');
                });

                die('<p style="color:green;">Subscription Invoice Sent</p>');
                //return View('invoice', compact('data'));
                return response()->json(['status' => 1,'message' => 'Subscription Invoice Sent', 'data' => []], 200);
                
            // }else{
            //      return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            // }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * This function use for view invoice to customer
     *
     * @return Response
     */
    public function viewInvoiceToCustomer(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sub_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            //$AuthController = new AuthController();
            //$token_status = $AuthController->tokenVerify($request);
            //if($token_status['status'] == '200'){
                $sub_id = $request->sub_id;
                

                $data = Subscription::where('subscription.id', $sub_id)->where('subscription.status', 'Completed')->join('transaction','subscription.id','=','transaction.subscription_id')->with('getcartdetails')->with('getCartAddonPackageDetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getRoutingDetails')->orderBy('subscription.id', 'DESC')->get();
                
                $data = $data[0];
                $send_view = ["data" => $data];
                $cust = ["email" => $data->getcartdetails->getUserDetails->email, "name" => $data->getcartdetails->getUserDetails->first_name];
                
                // Mail::send('invoice',$send_view,function($message) use ($cust){
                //     $message->to('Keerthi.kumar@clykk.com');
                //     $message->to('sarwanmawai@gmail.com');
                //     $message->subject('Subscription Invoice');
                // });
                return View('view', compact('data'));
                return response()->json(['status' => 1,'message' => 'Subscription Invoice Sent', 'data' => []], 200);
                
            // }else{
            //      return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            // }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }
    }

    /**
     * Get new subscription or not
     *
     * @return Response
     */
    public function getNewOrders(Request $request){
        try {
            $db_sec = $this->getConfigValueByKey('sub_new_alert');
            $from = date('Y-m-d H:i:s', strtotime('-'.$db_sec.' seconds'));
            $to = date('Y-m-d H:i:s');
           
            $count = Subscription::select('*')->where('subscription.created_at','>=', $from)->where('subscription.created_at', '<=', $to)->count();
            if($count >= 1){
                $datas = Subscription::select('*')->where('subscription.created_at','>=', $from)->where('subscription.created_at', '<=', $to)->get();
                $msg = '';
                foreach($datas as $data){
                    $msg .= '<p>New order ID '.$data->cart_id.' Subscription ID '.$data->id.' '.$data->status.', '.date('d-m-Y', strtotime($data->created_at)).', '.date('h:i A', strtotime($data->created_at)).'.</p>';
                }
                
                return response()->json(['status' => 1,'message' => $msg, 'data' => $data, 'count' => $count, 'from' => $from, 'to' => $to, 'db_sec' => $db_sec], 200);
            }else{
                return response()->json(['status' => 0,'message' => 'No New Subscription Are There!', 'data' => [], 'count' => $count, 'from' => $from, 'to' => $to, 'db_sec' => $db_sec], 200);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }
    }

    /**
     * Get new subscription or not
     *
     * @return Response
     */
    public function getNotAcceptedRoutes(Request $request){
        try {
            
            $datas = SPRoutingAlert::select('*')->where('no_of_route','>=', 2)->where('status', 'SEARCHING')->with('getCartPackageDetails')->with('getSubCategoryDetails')->get()->unique('subscription_id');
            
            
            $msg = '';
            foreach($datas as $data){
                $checkclose = SPRoutingAlert::select('*')->where('subscription_id',$data->subscription_id)->whereIn('status', ['COMPLETED', 'CLOSE'])->count();
                if($checkclose <=0){
                    $msg .= '<p> Order ID '.$data->cart_id.' Subscription ID '.$data->subscription_id.' '.$data->status.', Sub Cat:'.$data->getSubCategoryDetails->name.', Package: '.$data->getCartPackageDetails->package_name.' '.date('d-m-Y', strtotime($data->created_at)).', '.date('h:i A', strtotime($data->created_at)).'.</p>';
                }
            }
            if($msg != ''){
                return response()->json(['status' => 1,'message' => $msg, 'data' => $data, 'count' => count($datas)], 200);
            }else{
                return response()->json(['status' => 0,'message' => 'No Data Found!', 'data' => [], 'count' => count($datas)], 200);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }
    }

    /**
     * Get config data
     *
     * @Para $key string
     * @return Response
     */
    public function getConfigValueByKey($key){
        try {
            
            $value = Config::select('value')->where('configs.key','=', $key)->first();
            return $value['value'];

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }
    }
}

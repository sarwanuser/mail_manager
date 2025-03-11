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
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mail;
use Validator;
use DB;

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
            if($token_status['status'] == '200'){
                $sub_id = $request->sub_id;
                

                $data = Subscription::where('subscription.id', $sub_id)->where('subscription.status', 'Completed')->join('transaction','subscription.id','=','transaction.subscription_id')->with('getcartdetails')->with('getCartAddonPackageDetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getRoutingDetails')->orderBy('subscription.id', 'DESC')->get();
                $data = $data[0];
                
                $send_view = ["data" => $data];
                $cust = ["email" => $data->getcartdetails->getUserDetails->email, "name" => $data->getcartdetails->getUserDetails->first_name];
                
                Mail::send('invoice',$send_view,function($message) use ($cust){
                    //$message->to('Keerthi.kumar@clykk.com');
                    //$message->to('sarwanmawai@gmail.com');
                    $message->to($cust['email']);
                    $message->subject('Subscription Invoice');
                });

                die('<p style="color:green;">Subscription Invoice Sent</p>');
                //return View('invoice', compact('data'));
                return response()->json(['status' => 1,'message' => 'Subscription Invoice Sent', 'data' => []], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

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
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                $sub_id = $request->sub_id;
                

                $data = Subscription::where('subscription.id', $sub_id)->where('subscription.status', 'Completed')->join('transaction','subscription.id','=','transaction.subscription_id')->with('getcartdetails')->with('getCartAddonPackageDetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getRoutingDetails')->with('getSubTransactions')->orderBy('subscription.id', 'DESC')->get();
                
                $data = $data[0];
                
                //echo "<pre>";print_r($data); die('---------');
                $send_view = ["data" => $data];
                $cust = ["email" => $data->getcartdetails->getUserDetails->email, "name" => $data->getcartdetails->getUserDetails->first_name];
                //echo "<pre>";print_r($data);die('----');
                // Mail::send('invoice',$send_view,function($message) use ($cust){
                //     $message->to('Keerthi.kumar@clykk.com');
                //     $message->to('sarwanmawai@gmail.com');
                //     $message->subject('Subscription Invoice');
                // });
                return View('view', compact('data'));
                return response()->json(['status' => 1,'message' => 'Subscription Invoice Sent', 'data' => []], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
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
                return response()->json(['status' => 0,'message' => 'No Data Found!', 'data' => [], 'count' => 0], 200);
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

    /**
     * This function use for get the SP details by date for dashboard
     *
     * @return Response
     */
    public function getAllSPDataByDate(Request $request){
        $validator = Validator::make($request->all(), [ 
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                $from = date('Y-m-d 00:00:00', strtotime($request->from_date));
                $to = date('Y-m-d 23:59:59', strtotime($request->to_date));
                
                $datas = SPDetails::select('id as userId','first_name as firstName','last_name as lastName','email','email_verified as emailVerified','mobile','mobile_verified as mobileVerified','gender','dob','anniversary','picture','referral_code as referralCode','device_id as deviceId','device_type as deviceType','device_token as deviceToken','secret_hash','salt','auth_token','notification_enabled as notificationEnabled','last_login_at as lastLoginAt','active','enabled','created_at as createdAt','updated_at as updatedAt','country_code as countrycode','org_id as orgID','sub_org_id as subOrgID','location','rating','status','city_id as cityID','category_id as categoryID','role')->whereBetween('created_at', [$from, $to])->with('documents')->get()->toArray();
                
                return response()->json(['status' => 1,'message' => 'SP datas', 'users' => $datas], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }

    /**
     * This function use for get the SP details
     *
     * @return Response
     */
    public function getAllSPData(Request $request){
        $validator = Validator::make($request->all(), [ 
            'page' => 'required',
            'per_page' => 'required',
            //'filter' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                
                $datas = SPDetails::select('id as userId','first_name as firstName','last_name as lastName','email','email_verified as emailVerified','mobile','mobile_verified as mobileVerified','gender','dob','anniversary','picture','referral_code','device_id as deviceId','device_type as deviceType','device_token','secret_hash','salt','auth_token','notification_enabled as notificationEnabled','last_login_at','active','enabled','created_at as createdAt','updated_at as updatedAt','country_code','org_id as orgID','sub_org_id as subOrgID','location','rating','status','city_id','category_id as categoryID','role')->with('getcitydetails')->orWhere('first_name', 'like', '%' . $request->filter . '%')->orWhere('last_name', 'like', '%' . $request->filter . '%')->orWhere('mobile', 'like', '%' . $request->filter . '%')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();
                $spdatas = $datas['data'];
                $currentPage = $datas['current_page'];
                $totalCount = $datas['total'];
                $perPage = $datas['per_page'];
                $lastPage = $datas['last_page'];
                $x=0;
                foreach($spdatas as $data){
                    $sp_id = $data['userId'];
                    $cat_id = $data['categoryID'];
                    $spdatas[$x]['documents'] = DB::connection('sp_management')->select("select document_url as documentURL, status from document_received where referer_id ='$sp_id' and document_id in(select id from document_by_category where category_id ='$cat_id' and referer ='sp' and enabled ='1')");
                    
                    $y=0;
                    // foreach($data['documents'] as $doc){
                    //     $sdata = $this->getDocStatus($sp_id, $doc['id']);
                    //     $spdatas[$x]['documents'][$y]['status'] = $sdata['status'];
                    //     $spdatas[$x]['documents'][$y]['documentURL'] = $sdata['document_url'];
                    //     $y++;
                    // }  
                    $x++;
                }
                return response()->json(['status' => 1,'message' => 'SP datas', 'pageNumber' => $currentPage, 'count' => $totalCount, 'pageSize' => $totalCount, 'perPage' => $perPage, 'lastPage' => $lastPage, 'users' => $spdatas], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }
    
    /**
     * This function use for get the document status
     *
     * @return Response
     */
    public function getDocStatus($referer_id, $document_id){
        
        try {
            $duc_status = DocumentReceived::select('status','document_url')->where('referer_id', $referer_id)->where('document_id', $document_id)->limit(1)->get()->toArray();
            if(count($duc_status) > 0){
                return $duc_status[0];
            }else{
                return ['status' => '', 'document_url' => ''];
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }

    /**
     * This function use for get the SP details
     *
     * @return Response
     */
    public function getAllSubscriptions(Request $request){
        $validator = Validator::make($request->all(), [ 
            'page' => 'required',
            'per_page' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'city' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){ 
                $from = date('Y-m-d 00:00:00', strtotime($request->from_date));
                $to = date('Y-m-d 23:59:59', strtotime($request->to_date));
                if($request->filter != ''){
                    $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','city','sub_category_id','subCategoryName','category_id','categoryName')->orWhere('id', $request->filter)->orWhere('cart_id', $request->filter)->where('city',$request->city)->whereBetween('service_date', [$from, $to])->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();
                }else{
                    if($request->status == 'all' && $request->city != 'All'){
                        $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','city','sub_category_id','subCategoryName','category_id','categoryName')->whereBetween('service_date', [$from, $to])->where('city',$request->city)->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                    }elseif($request->status != 'all' && $request->city == 'All'){
                        $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','city','sub_category_id','subCategoryName','category_id','categoryName')->whereBetween('service_date', [$from, $to])->where('status',$request->status)->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                    }elseif($request->status == 'all' && $request->city == 'All'){
                        $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','city','sub_category_id','subCategoryName','category_id','categoryName')->whereBetween('service_date', [$from, $to])->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                    }else{
                        $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','city','sub_category_id','subCategoryName','category_id','categoryName')->whereBetween('service_date', [$from, $to])->where('city',$request->city)->where('status',$request->status)->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                    }
                }
                
                $spdatas = $datas['data'];
                $currentPage = $datas['current_page'];
                $totalCount = $datas['total'];
                $perPage = $datas['per_page'];
                $lastPage = $datas['last_page'];
                $allcitys = City::All();
                $x=0;
                
                return response()->json(['status' => 1,'message' => 'Subscriptions datas', 'currentPage' => $currentPage, 'maxPages' => $lastPage, 'subscriptions' => $spdatas, 'cities' => $allcitys], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }

    /**
     * This function use for get the all cart datas
     *
     * @return Response
     */
    public function getAllCartData(Request $request){
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                
                $datas = Cart::with('getUserDetails')->with('getPackageDetails')->where('status', 'cart')->orderBy('id', 'DESC')->get()->toArray();

                return response()->json(['status' => 1,'message' => 'Cart datas', 'data' => $datas], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }

    /**
     * This function use for get the viewed datas
     *
     * @return Response
     */
    public function getAllViewsData(Request $request){
        $validator = Validator::make($request->all(), [ 
            'user_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                
                $datas = PackagesViewed::with('getUserDetails')->with('getPackageDetails')->where('user_id', $request->user_id)->orderBy('id', 'DESC')->get()->toArray();

                return response()->json(['status' => 1,'message' => 'Package views datas', 'data' => $datas], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }

    /**
     * This function use for get the all share data
     *
     * @return Response
     */
    public function getAllSharesData(Request $request){
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                
                $share_datas = PackagesShare::select('user_id','package_id', 'created_at', 'shared_via', 'receiver_name', 'contact_info')->with('getUserDetails')->orderBy('id', 'DESC')->get()->toArray();

                return response()->json(['status' => 1,'message' => 'Share datas', 'data' => $share_datas], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }


    /**
     * This function use for get sp payments.
     *
     * @return Response
     */
    public function getSPPayments(Request $request){
        $validator = Validator::make($request->all(), [ 
            'page' => 'required',
            'per_page' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                // Check sp payment availble or not
                if($request->filter > 0){
                    $datas = SPPayment::with('getSubscriptionDetails')->with('getPaymentTransaction')->with('getSPDetails')->orWhere('id', $request->filter)->orWhere('subscription_id', $request->filter)->orWhere('sp_id', $request->filter)->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();
                }else{
                    $datas = SPPayment::with('getSubscriptionDetails')->with('getPaymentTransaction')->with('getSPDetails')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();
                }
                

                $SPPayment = $datas['data'];
                $currentPage = $datas['current_page'];
                $totalCount = $datas['total'];
                $perPage = $datas['per_page'];
                $lastPage = $datas['last_page'];

                return response()->json(['status' => 1,'message' => 'SP Payments!', 'currentPage' => $currentPage, 'maxPages' => $lastPage, 'totalCount' => $totalCount, 'data' => $SPPayment], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for make sp payments.
     *
     * @return Response
     */
    public function makeSPPayment(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required',
            'sp_id' => 'required',
            'transaction_id' => 'required',
            'payment_type' => 'required',
            'reference_no' => 'required',
            'total' => 'required',
            'payment_by' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                $SPTransaction = new SPTransaction();
                $SPTransaction->subscription_id = $request->subscription_id;
                $SPTransaction->sp_id = $request->sp_id;
                $SPTransaction->transaction_id = $request->transaction_id;
                $SPTransaction->payment_type = $request->payment_type;
                $SPTransaction->reference_no = $request->reference_no;
                $SPTransaction->total = $request->total;
                $SPTransaction->comment = $request->comment;
                $SPTransaction->payment_status = 'Done';
                $SPTransaction->payment_by = $request->payment_by;
                $SPTransaction->created_at = date('Y-m-d, H:i:s');
                $SPTransaction->save();  
                
                // Update the payment status of sp payment table
                $SPPayment = SPPayment::where('id', $request->payment_id)->first();
                $SPPayment->payment_status = 'done';
                $SPPayment->payment_date = date('Y-m-d, H:i:s');
                $SPPayment->save();
                

                return response()->json(['status' => 1,'message' => 'SP Payment Done!', 'data' => $SPTransaction], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }
}

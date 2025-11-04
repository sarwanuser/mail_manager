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
use  App\Models\SPDetailsByCatSubcat;
use  App\Models\UserDetails;
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
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
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
    public function getNotAcceptedRoutes(Request $request){
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
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
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
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
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token', 'token' => $request], 401);
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
                $where = [
                    'first_name' => $request->fname,
                    'last_name' => $request->lname,
                    'mobile' => $request->mobile,
                    'status' => $request->status,
                    'category_id' => $request->category,
                    'id' => $request->userId,
                    'city_id' => $request->city,
                ];
                $where = array_filter($where, function($value) {
                    return $value != "";
                });
                $datas = SPDetails::select('id as userId','first_name as firstName','last_name as lastName','email','email_verified as emailVerified','mobile','mobile_verified as mobileVerified','gender','dob','anniversary','picture','referral_code','device_id as deviceId','device_type as deviceType','device_token','secret_hash','salt','auth_token','notification_enabled as notificationEnabled','last_login_at','active','enabled','created_at as createdAt','updated_at as updatedAt','country_code','org_id as orgID','sub_org_id as subOrgID','location','rating','status','city_id','category_id as categoryID','role')->with('getcitydetails');
                foreach($where as $col => $val){
                    if($col == 'first_name' || $col == 'last_name' || $col == 'mobile'){
                        $datas->where($col, 'like', '%'.$val.'%');
                    }else{
                        $datas->where($col, $val);
                    }                    
                }
                
                $datas = $datas->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                $categories = Category::select('id','name')->where('enabled','1')->get();
                $cities = City::select('id','city_name')->where('enabled','1')->get();
                
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
                return response()->json(['status' => 1,'message' => 'SP datas', 'pageNumber' => $currentPage, 'count' => $totalCount, 'pageSize' => $totalCount, 'perPage' => $perPage, 'lastPage' => $lastPage, 'categories' => $categories, 'cities' => $cities, 'users' => $spdatas], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }

    /**
     * This function use for get the SP details by sub-category
     *
     * @return Response
     */
    public function getAllSPDataBySubcat(Request $request){

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                
                $datas = SPDetailsByCatSubcat::select('sp_id as id','first_name','last_name','email','mobile','active','enabled','category_id')->where('package_id', $request->package_id)->where('active', '1')->where('enabled', '1')->get(); 

                return response()->json(['status' => 1,'message' => 'SP datas', 'data' => $datas], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 401);
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
                    $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','city','state','sub_category_id','subCategoryName','category_id','categoryName')->with('getAddressDetails')->orWhere('id', $request->filter)->orWhere('cart_id', $request->filter)->where('city',$request->city)->whereBetween('service_date', [$from, $to])->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();
                }else{
                    if($request->status == 'all' && $request->city != 'All'){
                        $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','country','state','city','line1','line2','line3','landmark','pincode','latitude','longitude','sub_category_id','subCategoryName','category_id','categoryName')->whereBetween('service_date', [$from, $to])->where('city',$request->city)->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                    }elseif($request->status != 'all' && $request->city == 'All'){
                        $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','country','state','city','line1','line2','line3','landmark','pincode','latitude','longitude','sub_category_id','subCategoryName','category_id','categoryName')->whereBetween('service_date', [$from, $to])->where('status',$request->status)->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                    }elseif($request->status == 'all' && $request->city == 'All'){
                        $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','country','state','city','line1','line2','line3','landmark','pincode','latitude','longitude','sub_category_id','subCategoryName','category_id','categoryName')->whereBetween('service_date', [$from, $to])->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                    }else{
                        $datas = SubscriptionDatas::select('cart_id as cartID','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt','country','state','city','line1','line2','line3','landmark','pincode','latitude','longitude','sub_category_id','subCategoryName','category_id','categoryName')->whereBetween('service_date', [$from, $to])->where('city',$request->city)->where('status',$request->status)->distinct('id')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

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
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 401);
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
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 401);
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
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 401);
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
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 401);
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
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 500);
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
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for get vendor list by subscription id.
     *
     * @return Response
     */
    public function getVendorListBySubsId(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                if(Subscription::where('id', $request->subscription_id)->count() == 0){
                    return response()->json(['error' => 1,'message' => 'Subscription Not Found!'], 401);
                }
                $Subscriptions = DB::connection('cart_management')->select("select sub.*, cp.sub_category_id  from cart_management.subscription sub join cart_package cp on cp.cartID = sub.cart_id  where sub.id=".$request->subscription_id)[0];

                $city_id = $this->getCityIdBySubId($Subscriptions->cart_id);
                
                $service_rule = SubCategoryServiceRule::where('sub_category_id', $Subscriptions->sub_category_id)->first();
                $rule_code = $this->getRuleCode($service_rule->service_rule_id);
                //die('-------$Subscriptions->sub_category_id '. $Subscriptions->sub_category_id. '-----$rule_code '.$rule_code);
                if(!empty($city_id)){
                    $vendor_list = DB::connection('sp_management')->select("SELECT * FROM sp_service_settings spss JOIN sp_detail sd on sd.id = spss.sp_id where spss.subcategory_id = '".$Subscriptions->sub_category_id."' and spss.".$rule_code." = '1' and spss.enabled = '1' and sd.city_id = $city_id and sd.`role` = 'provider' and sd.status ='approved'");
                }else{
                    $vendor_list = DB::connection('sp_management')->select("SELECT * FROM sp_service_settings spss JOIN sp_detail sd on sd.id = spss.sp_id where spss.subcategory_id = '".$Subscriptions->sub_category_id."' and spss.".$rule_code." = '1' and spss.enabled = '1' and sd.`role` = 'provider' and sd.status ='approved'");
                }
                
                // foreach($vendor_list as $vendor){
                //     $vendor->city_name'] = $vendor->org_id;
                //     $vendor['org_name'] = $vendor->sub_org_id;
                //     $vendor['sub_org_name'] = $vendor->city_id;
                // }

                //$vendor_list = SPServiceSettings::where('subcategory_id', $routing_details->sub_category_id)->where('enabled', '1')->where($rule_code, '1')->with('getSPdetails')->get();
                return response()->json(['status' => 1,'message' => 'SP List For Manual Routing', 'data' => $vendor_list], 200);                
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }

    /**
     * This function use for get the city id by sub_id
     * @para $cart_id
     * @return $city_id
     */
    public function getCityIdBySubId($cart_id){
        try {
            $address = DB::connection('cart_management')->select("select * from cart_management.address where cartID=".$cart_id)[0];

            $city = DB::connection('location_service')->select("select * from location_service.city where city_name = '$address->city'")[0];
            return $city->id;
        }catch(\Exception $e) {
            return '';
        }
    }

    /**
     * This function use for get route rule code by route rule id.
     *
     * @return Response
     */
    public function getRuleCode($rule_id){
        
        try {
            if($rule_id == 1){
                return 's2_c';
            }elseif($rule_id == 2){
                return 'c2_s';
            }elseif($rule_id == 3){
                return 's2_vc';
            }elseif($rule_id == 4){
                return 's2_c2_c';
            }else{
                return '';
            }
        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for get routing details by subscription id.
     *
     * @return Response
     */
    public function getRoutingDetailsBySubsId(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                $routing_details = SPRoutingAlert::where('subscription_id', $request->subscription_id)->with('getroutngdetails')->get();
                if($routing_details->count() > 0){
                    return response()->json(['status' => 1,'message' => 'Subscription routing details', 'data' => $routing_details], 200);
                }else{
                    return response()->json(['status' => 0,'message' => 'Subscription routing details not found', 'data' => $routing_details], 200);
                }
                
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }

    /**
     * This function use for get routing details by routing id.
     *
     * @return Response
     */
    public function getRoutingDetailsById(Request $request){
        $validator = Validator::make($request->all(), [ 
            'routing_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                $routing_details = SPRoutingAlert::where('id', $request->routing_id)->first();
                if($routing_details->count() > 0){
                    if(@$request->provider_id){
                        $routing_list = SPRoutingAlertDetail::where('sp_routing_id', $request->routing_id)->where('provider_id', $request->provider_id)->get();
                    }else{
                        $routing_list = SPRoutingAlertDetail::where('sp_routing_id', $request->routing_id)->get();
                    }
                    
                    return response()->json(['status' => 1,'message' => 'Routing details', 'data' => $routing_list], 200);
                }else{
                    return response()->json(['status' => 0,'message' => 'Routing details not found', 'data' => $routing_details], 200);
                }
                
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }

    /**
     * This function use for get the SP details
     *
     * @return Response
     */
    public function getSPRatings(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sp_id' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                
                $ratings = SPServiceRating::Where('sp_id', $request->sp_id)->get();

                $vagrating = DB::connection('sp_management')->select("select AVG(sp_management.sp_service_rating.sp_rating) as ratingavg from sp_management.sp_service_rating where sp_management.sp_service_rating.sp_id =".$request->sp_id)[0];
                
                return response()->json(['status' => 1,'message' => 'SP Ratings', 'ratings' => $ratings, 'vagrating' => $vagrating], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }

    /**
     * This function use for get accepted and assign order list by sp_id
     *
     * @return Response
     */
    public function getUpcomingOrderBySPId(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sp_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                $routing_details = SPRoutingAlert::where('status', '!=', 'Completed')->where('accept_provider_id', $request->sp_id)->with('getsubsdetails')->with('getcartdetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getSPDetails')->with('getUserDetails')->get();
                if($routing_details->count() > 0){
                    return response()->json(['status' => 1,'message' => 'Upcoming orders', 'data' => $routing_details], 200);
                }else{
                    return response()->json(['status' => 0,'message' => 'Upcoming order not found', 'data' => $routing_details], 200);
                }
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }

    /**
     * This function use for get vendor list by subscription id.
     *
     * @return Response
     */
    public function testAPI(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                if(Subscription::where('id', $request->subscription_id)->count() == 0){
                    return response()->json(['error' => 1,'message' => 'Subscription Not Found!'], 401);
                }
                $Subscriptions = DB::connection('cart_management')->select("select sub.*, cp.sub_category_id  from cart_management.subscription sub join cart_package cp on cp.cartID = sub.cart_id  where sub.id=".$request->subscription_id)[0];

                $city_id = $this->getCityIdBySubId($Subscriptions->cart_id);
                
                $service_rule = SubCategoryServiceRule::where('sub_category_id', $Subscriptions->sub_category_id)->first();
                $rule_code = $this->getRuleCode($service_rule->service_rule_id);
                //die('-------$Subscriptions->sub_category_id '. $Subscriptions->sub_category_id. '-----$rule_code '.$rule_code);
                if(!empty($city_id)){
                    $vendor_list = DB::connection('sp_management')->select("SELECT * FROM sp_service_settings spss JOIN sp_detail sd on sd.id = spss.sp_id where spss.subcategory_id = '".$Subscriptions->sub_category_id."' and spss.".$rule_code." = '1' and spss.enabled = '1' and sd.city_id = $city_id and sd.`role` = 'provider' and sd.status ='approved'");
                }else{
                    $vendor_list = DB::connection('sp_management')->select("SELECT * FROM sp_service_settings spss JOIN sp_detail sd on sd.id = spss.sp_id where spss.subcategory_id = '".$Subscriptions->sub_category_id."' and spss.".$rule_code." = '1' and spss.enabled = '1' and sd.`role` = 'provider' and sd.status ='approved'");
                }
                

                //$vendor_list = SPServiceSettings::where('subcategory_id', $routing_details->sub_category_id)->where('enabled', '1')->where($rule_code, '1')->with('getSPdetails')->get();
                return response()->json(['status' => 1,'message' => 'SP List For Manual Routing', 'data' => $vendor_list], 200);                
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }

    /**
     * This function use for get vendor list by subscription id.
     *
     * @return Response
     */
    public function speedTest(Request $request){
        
        try {
            $sp_list = DB::connection('sp_management')->select("SELECT * FROM sp_detail");
            return response()->json(['status' => 1,'message' => 'SP List', 'data' => $sp_list], 200);                
           

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }

    /**
     * This function use for get the order details
     *
     * @return Response
     */
    public function getAllOrders(Request $request){
        $validator = Validator::make($request->all(), [ 
            'page' => 'required',
            'per_page' => 'required',
            //'order_id' => 'required',
            // 'customer_id' => 'required',
            // 'city' => 'required',
            // 'status' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){ 
                // $datas = BookingOrder::with('getSubscriptions')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();

                if($request->sub_id != '' && $request->order_id == ''){
                    $datas = Subscription::select('cart_id as cartID','cart_id','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt')->with('cartData')->with('getQnaDetails')->with('serviceAddress')->with('deliveryAddress')->with('schedule')->with('getCartPackageDetails')->with('getAddonPackageDetails')->with('getSubTransactions')->with('getTransactionDetails')->orderBy('id', 'DESC')->where('id', $request->sub_id)->paginate($request->per_page)->toArray();
                }elseif($request->sub_id == '' && $request->order_id != ''){
                    $datas = Subscription::select('cart_id as cartID','cart_id','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt')->with('cartData')->with('getQnaDetails')->with('serviceAddress')->with('deliveryAddress')->with('schedule')->with('getCartPackageDetails')->with('getAddonPackageDetails')->with('getSubTransactions')->with('getTransactionDetails')->orderBy('id', 'DESC')->where('cart_id', $request->order_id)->paginate($request->per_page)->toArray();
                }elseif($request->order_id != '' && $request->sub_id != ''){
                    $datas = Subscription::select('cart_id as cartID','cart_id','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt')->with('cartData')->with('getQnaDetails')->with('serviceAddress')->with('deliveryAddress')->with('schedule')->with('getCartPackageDetails')->with('getAddonPackageDetails')->with('getSubTransactions')->with('getTransactionDetails')->orderBy('id', 'DESC')->where('id', $request->sub_id)->where('cart_id', $request->order_id)->paginate($request->per_page)->toArray();
                }else{
                    $datas = Subscription::select('cart_id as cartID','cart_id','created_at as createdAt','id','resched_count as reschedCount','service_date as serviceDate', 'service_time as serviceTime', 'status','updated_at as updatedAt')->with('cartData')->with('getQnaDetails')->with('serviceAddress')->with('deliveryAddress')->with('schedule')->with('getCartPackageDetails')->with('getAddonPackageDetails')->with('getSubTransactions')->with('getTransactionDetails')->orderBy('id', 'DESC')->paginate($request->per_page)->toArray();
                }
                
                
                $spdatas = $datas['data'];
                $currentPage = $datas['current_page'];
                $totalCount = $datas['total'];
                $perPage = $datas['per_page'];
                $lastPage = $datas['last_page'];
                $allcitys = City::All();
                $x=0;
                
                return response()->json(['status' => 1,'message' => 'Order datas', 'currentPage' => $currentPage, 'maxPages' => $lastPage, 'orders' => $spdatas, 'cities' => $allcitys], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }
    
    /**
     * This function use for update the service date and time
     *
     * @return Response
     */
    public function updateServiceDateTime(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required',
            'service_date' => 'required',
            'service_time' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){  
                
                // Update the service date and time 
                $Subscription = Subscription::where('id', $request->subscription_id)->first();
                $Subscription->service_time = $request->service_time;
                $Subscription->service_date = date('Y-m-d', strtotime($request->service_date));
                $Subscription->save();
                
                return response()->json(['status' => 1,'message' => 'Updated the service date and time', 'data' => $Subscription], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for update the service address
     *
     * @return Response
     */
    public function updateServiceAddress(Request $request){
        $validator = Validator::make($request->all(), [ 
            'cart_id' => 'required',
            // 'line1' => 'required',
            'line2' => 'required',
            // 'line3' => 'required',
            'landmark' => 'required',
            'pincode' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){  
                
                // Update the service address
                $Address = Address::where('cartID', $request->cart_id)->first();
                // $Address->line1 = $request->line1;
                $Address->line2 = $request->line2;
                // $Address->line3 = $request->line3;
                $Address->landmark = $request->landmark;
                $Address->pincode = $request->pincode;
                $Address->latitude = $request->latitude;
                $Address->longitude = $request->longitude;
                $Address->save();
                
                return response()->json(['status' => 1,'message' => 'Updated the service address', 'data' => $Address], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for update the user active status
     *
     * @return Response
     */
    public function updateUserStatus(Request $request){
        $validator = Validator::make($request->all(), [ 
            'user_id' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                $user = UserDetails::where('id', $request->user_id)->first();
                $user->active = ($user->active == 1)?0:1;
                $user->save();
                return response()->json(['status' => 1,'message' => 'Update Active Status Successfull', 'users' => $user], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token', 'token' => $request], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }

    /**
     * This function use for send invite to single user
     *
     * @return Response
     */
    public function sendInviteToSingleUser(Request $request){ 
        try {
            $data = $request->all();
            // Mail
            $send_view = ["data" => $data];
            $cust = ["email" => $data['email'], "name" => $data['name'], "class" => $data['class_name']];
            
            Mail::send('single_invite',$send_view,function($message) use ($cust){
                $message->to($cust['email']);
                $message->subject('Class Invite - '.$cust['class']);
            });

            die('<p style="color:green;">Sent Invites - '.$data['email'].'</p>');

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * This function use for send invite to users
     *
     * @return Response
     */
    public function sendInviteToUsers(Request $request){ 
        try {

            // Mail
            $datas = DB::connection('clykk_lifestyle')->select('select * from invites where type="mail" and sent != "1"');

            $emails = '';
            foreach($datas as $data){
                $send_view = ["data" => $data];
                $cust = ["email" => $data->email, "name" => $data->name, "class" => $data->class_name];
                
                Mail::send('class_invite',$send_view,function($message) use ($cust){
                    $message->to($cust['email']);
                    $message->subject('Class Invite - '.$cust['class']);
                });
                
                $emails .= $data->email;

                DB::connection('clykk_lifestyle')->statement('UPDATE invites SET sent = ? WHERE id = ?', ['1', $data->id]);
            }

            die('<p style="color:green;">Sent Invites - '.$emails.'</p>');

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * This function use for delete the last day send mail data
     *
     * @return Response
     */
    public function deleteInviteDatas(Request $request){ 
        try {
            DB::connection('clykk_lifestyle')->statement('delete from invites WHERE sent = ? and created_at = ?', ['1', date('Y-m-d', strtotime('-1 day'))]);

            die('<p style="color:red;">Deleted Invites</p>');

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }
}

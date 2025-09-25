<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use  App\Models\ClassSession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mail;
use Validator;
use DB;
use DateTime;
use DateInterval;
use DatePeriod;

class ClassSessionController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * This function use for generate a class sessions
     *
     * @return Response
     */
    public function generateClassSessions(Request $request){
        
        $validator = Validator::make($request->all(), [ 
            'sub_category_id' => 'required',
            'package_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'class_time' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                $req = $request->all();
                $class_times = $req['class_time'];//json_decode($request->class_time, true); 
                $class_times = array_column((array)$class_times, 'value');
                $class_gen_count = 0;
                $dates = $this->getDatesBetween((string)$request->from_date, $request->to_date);
                foreach($dates as $date){
                    foreach($class_times as $time){
                        dd($time);
                        $datas = ClassSession::select('id')->where('package_id', $request->package_id)->where('class_date', $date)->where('class_time', $time)->count();
                        if($datas < 1){
                            $ClassSession = new ClassSession();
                            $ClassSession->sub_category_id = $request->sub_category_id;
                            $ClassSession->package_id = $request->package_id;
                            $ClassSession->class_date = $date;
                            $ClassSession->class_time = $time;
                            $ClassSession->status = 'scheduled';
                            $ClassSession->enabled = 1;
                            $ClassSession->created_at = date('Y-m-d, H:i:s');
                            $ClassSession->save(); 
                            $class_gen_count++;
                        }
                    }
                }
                
                return response()->json(['status' => 1,'message' => 'Generate Class Session Successfull, Class generated - '.$class_gen_count, 'data' => []], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    function getDatesBetween($startDate, $endDate) {
        dd($startDate);
        $dates = [];
    
        $start = new DateTime($startDate);
        $end   = new DateTime($endDate);
    
        // Include the end date
        $end->modify('+1 day');
    
        $interval = new DateInterval('P1D'); // 1 day interval
        $period   = new DatePeriod($start, $interval, $end);
    
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }
    
        return $dates;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getClassSession(Request $request){
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                
                if(!isset($request->id) && $request->id == ''){
                    $datas = ClassSession::select('id','package_id','class_date','class_time','sp_id','join_url','status','sub_category_id','package_name','package_image_url','created_at','updated_at','enabled')->orderBy('created_at', 'DESC')->get();
                }else{
                    $datas = ClassSession::select('id','package_id','class_date','class_time','sp_id','join_url','status','sub_category_id','package_name','package_image_url','created_at','updated_at','enabled')->where('id', $request->id)->orderBy('created_at', 'DESC')->first();
                }
                
                return response()->json(['status' => 1,'message' => 'List of Class Sessions', 'data' => $datas], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * This function use for update the class session data
     *
     * @return Response
     */
    public function updateClassSession(Request $request){
        $validator = Validator::make($request->all(), [ 
            'class_session_id' => 'required',
            'class_date' => 'required',
            'class_time' => 'required',
            // 'sp_id' => 'required',
            // 'join_url' => 'required',
            'status' => 'required',
            'enabled' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){
                $classsession = ClassSession::where('id', $request->class_session_id)->first();
                $classsession->status = $request->status;
                $classsession->enabled = $request->enabled;
                $classsession->class_date = $request->class_date;
                $classsession->class_time = $request->class_time;
                $classsession->sp_id = $request->sp_id;
                $classsession->join_url = $request->join_url;
                $classsession->save();
                return response()->json(['status' => 1,'message' => 'Update Class Session Successfull', 'users' => $classsession], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'Unauthorized auth token', 'token' => $request], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }
    }
}

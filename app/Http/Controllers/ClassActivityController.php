<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use  App\Models\ClassSession;
use  App\Models\ClassBooking;
use  App\Models\ViewClassBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Mail;
use Validator;
use DB;
use DateTime;
use DateInterval;
use DatePeriod;

class ClassActivityController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getClassBookings(Request $request){
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                
                if(!isset($request->id) && $request->id == ''){
                    $datas = ViewClassBooking::orderBy('class_date', 'DESC')->get();
                }else{
                    $datas = ViewClassBooking::where('id', $request->id)->first();
                }
                
                return response()->json(['status' => 1,'message' => 'List of Class Booking', 'data' => $datas], 200);
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }

    /**
     * This function use for generate the send invite to user
     *
     * @return \Illuminate\Http\Response
     */
    public function generateClassInvite(Request $request){
        try {
            $datas = ClassBooking::with('getSession')->with('getUser')->orderBy('created_at', 'DESC')->get();
            foreach($datas as $data){
                $date1 = new DateTime();
                $date2 = new DateTime($data->getSession->class_date.' '.$data->getSession->class_time);
                
                $diffInMinutes = abs($date1->getTimestamp() - $date2->getTimestamp()) / 60;
                
                if ($diffInMinutes == 60) {
                    DB::connection('clykk_lifestyle')->statement("INSERT INTO invites (name, email, mobile, `type`, class_date, class_time, class_link, created_at, updated_at, class_name, sent) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$data->getUser->first_name.' '.$data->getUser->last_name, $data->getUser->email, $data->getUser->mobile, 'mail', $data->getSession->class_date, $data->getSession->class_time, $data->getSession->join_url, date('Y-m-d, H:i:s'), date('Y-m-d, H:i:s'), $data->getSession->getPackage->package_name, 0]);

                    DB::connection('clykk_lifestyle')->statement("INSERT INTO invites (name, email, mobile, `type`, class_date, class_time, class_link, created_at, updated_at, class_name, sent) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$data->getUser->first_name.' '.$data->getUser->last_name, $data->getUser->email, $data->getUser->mobile, 'notification', $data->getSession->class_date, $data->getSession->class_time, $data->getSession->join_url, date('Y-m-d, H:i:s'), date('Y-m-d, H:i:s'), $data->getSession->getPackage->package_name, 0]);

                    DB::connection('clykk_lifestyle')->statement("INSERT INTO invites (name, email, mobile, `type`, class_date, class_time, class_link, created_at, updated_at, class_name, sent) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$data->getUser->first_name.' '.$data->getUser->last_name, $data->getUser->email, $data->getUser->mobile, 'whatsapp', $data->getSession->class_date, $data->getSession->class_time, $data->getSession->join_url, date('Y-m-d, H:i:s'), date('Y-m-d, H:i:s'), $data->getSession->getPackage->package_name, 0]);

                }
                // elseif($diffInMinutes == 30) {
                //     DB::connection('clykk_lifestyle')->statement("INSERT INTO invites (name, email, mobile, `type`, class_date, class_time, class_link, created_at, updated_at, class_name, sent) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$data->getUser->first_name.' '.$data->getUser->last_name, $data->getUser->email, $data->getUser->mobile, 'mail', $data->getSession->class_date, $data->getSession->class_time, $data->getSession->join_url, date('Y-m-d, H:i:s'), date('Y-m-d, H:i:s'), $data->getSession->getPackage->package_name, 0]);
                // }elseif($diffInMinutes == 15) {
                //     DB::connection('clykk_lifestyle')->statement("INSERT INTO invites (name, email, mobile, `type`, class_date, class_time, class_link, created_at, updated_at, class_name, sent) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$data->getUser->first_name.' '.$data->getUser->last_name, $data->getUser->email, $data->getUser->mobile, 'mail', $data->getSession->class_date, $data->getSession->class_time, $data->getSession->join_url, date('Y-m-d, H:i:s'), date('Y-m-d, H:i:s'), $data->getSession->getPackage->package_name, 0]);
                // }
            }
            die('Done');

        }catch(\Exception $e) {
            die('<p style="color:red;">Error: '.$e->getMessage()."</p>");
            return response()->json(['message' => 'error: '.$e->getMessage()], 500);
        }
    }
}

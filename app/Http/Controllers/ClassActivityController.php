<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use  App\Models\ClassSession;
use  App\Models\ClassBooking;
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
                    $datas = ClassBooking::with('getUser')->with('getSession')->orderBy('created_at', 'DESC')->get();
                }else{
                    $datas = ClassBooking::where('id', $request->id)->with('getUser')->with('getSession')->first();
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
}

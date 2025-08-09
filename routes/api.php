<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Send invoice to customer
$router->get('sendinvoicetocustomer', 'App\Http\Controllers\Controller@sendInvoiceToCustomer');
$router->get('viewinvoice', 'App\Http\Controllers\Controller@viewInvoiceToCustomer');
$router->get('getneworders', 'App\Http\Controllers\Controller@getNewOrders');
$router->get('getnotacceptroutes', 'App\Http\Controllers\Controller@getNotAcceptedRoutes');

// This route for get all sp data by date for dashboard
Route::get('/allspdatabydate', 'App\Http\Controllers\Controller@getAllSPDataByDate');

// This route for get all SP data
Route::get('/allspdatas', 'App\Http\Controllers\Controller@getAllSPData');

// This route for get all subscription data
Route::get('/getallsubscriptions', 'App\Http\Controllers\Controller@getAllSubscriptions');

// This route for get all cart data
Route::get('/allcartdata', 'App\Http\Controllers\Controller@getAllCartData');

// This route for get all cart data
Route::get('/allpackageviewsdata', 'App\Http\Controllers\Controller@getAllViewsData');

 // This route for get all share datas
 Route::get('/allshares', 'App\Http\Controllers\Controller@getAllSharesData');

 // Get SP Payments
 $router->get('getsppayments', 'App\Http\Controllers\Controller@getSPPayments');

 // Get SP Payments
 $router->get('getspratings', 'App\Http\Controllers\Controller@getSPRatings');

 // Get upcoming orders by sp id
 $router->get('getupcomingorders', 'App\Http\Controllers\Controller@getUpcomingOrderBySPId');

 // Get SP Payments
 $router->post('makesppayment', 'App\Http\Controllers\Controller@makeSPPayment');

 // Get SP list by subscription id
 $router->get('getsplist', 'App\Http\Controllers\Controller@getVendorListBySubsId');

// test API
$router->get('apitest', 'App\Http\Controllers\Controller@testAPI');

// Speed Test
$router->get('speedtest', 'App\Http\Controllers\Controller@speedTest');

// get order list api
$router->get('getorderlist', 'App\Http\Controllers\Controller@getAllOrders');

// get grand app passcode by email
$router->get('getgrandapppasscode', 'App\Http\Controllers\GrandAppController@getPassCodeByEmail');

// Create new senior in grand app
$router->post('createnewsenior', 'App\Http\Controllers\GrandAppController@createNewSenior');

// Updated the service date and time by sub_id
$router->post('updateservicedatetime', 'App\Http\Controllers\Controller@updateServiceDateTime');

// Updated the service address by cart_id
$router->post('updateserviceaddress', 'App\Http\Controllers\Controller@updateServiceAddress');

// Updated active status by userid
$router->post('updateuserstatus', 'App\Http\Controllers\Controller@updateUserStatus');



 
// Get routing details
$router->get('getroutingdetails', 'App\Http\Controllers\Controller@getRoutingDetailsBySubsId');

// Get routing details by id
$router->get('getroutingdetailsbyid', 'App\Http\Controllers\Controller@getRoutingDetailsById');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Get all elders list for 25*7 Profile
Route::resource('/elders', 'App\Http\Controllers\ElderManagment');

// Get elder subscriptions by elder_id
Route::resource('/elder-subscriptions', 'App\Http\Controllers\ElderSubscription');

// Get elder residences by elder_id
Route::resource('/residences', 'App\Http\Controllers\ElderResidences');

// Get elder family members by elder_id
Route::resource('/elder-family', 'App\Http\Controllers\ElderFamilyMember');



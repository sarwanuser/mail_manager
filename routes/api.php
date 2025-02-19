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
 Route::get('/allshares', 'RoutingController@getAllSharesData');



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

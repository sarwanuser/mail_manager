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




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

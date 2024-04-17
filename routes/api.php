<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/addevent',[EventController::class,'addEvent']);
Route::post('/register',[UserController::class,'registerUser']);
Route::post('/login',[UserController::class,'loginUser']);
Route::post('/bookticket',[EventController::class,'bookTicket']);
Route::post('/viewbookings',[EventController::class,'viewBookingDetails']);


Route::get('/getevents',[EventController::class,'getEvent']);
Route::get('/alleventdetails',[EventController::class,'getallEventDetails']);
Route::get('/allbookingdetails',[EventController::class,'getallBookingDetails']);
Route::get('/getsearchedevents',[EventController::class,'getSearchedEvent']);




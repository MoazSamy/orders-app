<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrdersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function(){
    Route::post('users/register', 'register');
    Route::post('users/login', 'login');

    Route::get('users/logout', 'logout')->middleware('auth:sanctum');
});

Route::controller(OrdersController::class)->group(function(){
    Route::post('orders/create', 'store');
    Route::post('orders/delete/{id}', 'destroy')->middleware('auth:sanctum');
    Route::post('orders/update/{id}', 'update')->middleware('auth:sanctum');
    Route::post('orders/assign/{orderId}/person/{deliveryPersonnelId}','AssignDeliveryPersonnel')->middleware('auth:sanctum');
    Route::post('orders/update/{orderId}/status/{orderStatus}','ChangeOrderStatus')->middleware('auth:sanctum');
    
    Route::get('orders/','index')->middleware('auth:sanctum');
    Route::get('orders/{id}','show')->middleware('auth:sanctum');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

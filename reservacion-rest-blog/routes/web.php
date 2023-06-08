<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HuespedController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;

Route::prefix('api')->group(function (){
    Route::resource('/huesped', HuespedController::class,['except'=>['create','edit']]);
    Route::resource('/pago', PagoController::class,['except'=>['create','edit']]);
    Route::resource('/reserva', ReservaController::class,['except'=>['create','edit']]);
    Route::resource('/user', UserController::class,['except'=>['create','edit']]);
    Route::resource('/habitacion', HabitacionController::class,['except'=>['create','edit']]);
    Route::post('/user/login',[UserController::class,'login']);
    Route::post('/user/getidentity',[UserController::class,'getIdentity']);
    
    Route::get('/user/getimage/{filename}',[UserController::class,'getImage']);
    Route::post('/user/upload',[UserController::class,'uploadImage']);
    Route::get('/habitacion/getimage/{filename}',[HabitacionController::class,'getImage']);
    Route::post('/habitacion/upload',[HabitacionController::class,'uploadImage']);
});
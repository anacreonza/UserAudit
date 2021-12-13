<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [DeviceController::class, 'index']);
Route::get('/device/index', [DeviceController::class, 'index']);
Route::get('/device/create', [DeviceController::class, 'create']);
Route::get('/device/store', [DeviceController::class, 'store']);
Route::get('/device/view/{id}', [DeviceController::class, 'view']);
Route::get('/device/delete/{id}', [DeviceController::class, 'destroy']);

Route::get('/user/index', [UserController::class, 'index']);
Route::get('/user/create', [UserController::class, 'create']);
Route::get('/user/store', [UserController::class, 'store']);
Route::get('/user/view/{id}', [UserController::class, 'view']);
Route::get('/user/delete/{id}', [UserController::class, 'delete']);
Route::get('/journalentry/index', function(){
    return view('journalentry_index');
});
// REST endpoints for reports
Route::get('/report/index', [ReportController::class, 'index']);
Route::get('/report/view/{id}', [ReportController::class, 'read']);
Route::get('/report/delete/{id}', [ReportController::class, 'delete']);
Route::post('/report', [ReportController::class, 'store']);

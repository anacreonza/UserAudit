<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
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
Route::get('/create_device_report', [DeviceController::class, 'create']);
Route::post('/store_device_report', [DeviceController::class, 'store']);
Route::get('/show_device_report', [DeviceController::class, 'show']);
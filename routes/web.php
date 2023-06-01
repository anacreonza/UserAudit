<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\SearchController;
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

// Route::get('/', [DeviceController::class, 'index']);
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/device/index', [DeviceController::class, 'index']);
Route::get('/device/create/{user_id}', [DeviceController::class, 'create']);
Route::get('/device/create', [DeviceController::class, 'create']);
Route::get('/device/edit/{id}', [DeviceController::class, 'edit']);
Route::post('/device/update/{id}', [DeviceController::class, 'update']);
Route::post('/device/store', [DeviceController::class, 'store']);
Route::get('/device/view/{id}', [DeviceController::class, 'view']);
Route::get('/device/delete/{id}', [DeviceController::class, 'destroy']);
Route::get('/device/export/csv', [DeviceController::class, 'export_csv']);

// Client routes
Route::get('/client/index', [ClientController::class, 'index']);
Route::get('/client/edit/{id}', [ClientController::class, 'edit']);
Route::post('/client/update/{id}', [ClientController::class, 'update']);
Route::get('/client/create', [ClientController::class, 'create']);
Route::get('/client/lookup/{id}', [ClientController::class, 'lookup']);
Route::post('/client/store', [ClientController::class, 'store']);
Route::get('/client/view/{id}', [ClientController::class, 'view']);
Route::get('/client/delete/{id}', [ClientController::class, 'delete']);
Route::get('/client/export/csv', [ClientController::class, 'export_csv']);
// Journal routes
Route::get('/journal_entry/create/{id}', [JournalEntryController::class, 'create']);
Route::post('/journal_entry/store/{id}', [JournalEntryController::class, 'store']);
Route::delete('/journal_entry/delete/{id}', [JournalEntryController::class, 'delete']);
Route::get('/journal_entry/index', [JournalEntryController::class, 'index']);
// REST endpoints for reports
Route::get('/report/index', [ReportController::class, 'index']);
Route::get('/report/view/{id}', [ReportController::class, 'read']);
Route::get('/report/delete/{id}', [ReportController::class, 'delete']);
Route::post('/report/store/', [ReportController::class, 'store']);
// Search endpoints
Route::post('/clients/search/', [SearchController::class, 'filter_clients']);
Route::post('/reports/search/', [SearchController::class, 'filter_reports']);
Route::post('/devices/search/', [SearchController::class, 'filter_devices']);
Route::post('/journal_entries/search/', [SearchController::class, 'filter_journalentries']);
// External request endpoints
Route::get('/retrieve/mr/{serial}', [DeviceController::class, 'retrieve_mac_details']);

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/logout', [App\Http\Controllers\UserController::class, 'logout']);

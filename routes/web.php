<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/export', [App\Http\Controllers\ExportController::class,'export'])->name('export');
Route::get('/department', [App\Http\Controllers\ExportController::class,'getDepartment'])->name('get.department');
Route::get('/employee', [App\Http\Controllers\ExportController::class,'getEmployee'])->name('get.employee');
Route::get('/data', [App\Http\Controllers\ExportController::class,'getData'])->name('get.data');

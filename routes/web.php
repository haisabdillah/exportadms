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

Route::get('/', [App\Http\Controllers\HomeController::class,'view'])->name('home');
Route::get('/devices', [App\Http\Controllers\HomeController::class,'devices'])->name('home.devices');
Route::get('/export-attendance', [App\Http\Controllers\ExportController::class,'view'])->name('export.view');
Route::post('/export', [App\Http\Controllers\ExportController::class,'export'])->name('export');
Route::get('/department', [App\Http\Controllers\ExportController::class,'getDepartment'])->name('get.department');
Route::get('/employee', [App\Http\Controllers\ExportController::class,'getEmployee'])->name('get.employee');
Route::get('/data', [App\Http\Controllers\ExportController::class,'getData'])->name('get.data');
Route::get('/attendance', [App\Http\Controllers\AttendanceController::class,'view'])->name('attendance.view');
Route::post('/attendance', [App\Http\Controllers\AttendanceController::class,'export'])->name('attendate.generate');


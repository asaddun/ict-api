<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AndonController;
use App\Http\Controllers\CopierController;
use App\Http\Controllers\LockerController;
use App\Http\Controllers\SensorController;

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

Route::get('/auth', [AuthController::class, 'index'])->name('auth.index');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');


Route::middleware(['checkLogin'])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/andon', [AndonController::class, 'index'])->name('andon');
    Route::get('/sensor', [SensorController::class, 'index'])->name('sensor');

    Route::get('/locker/active', [LockerController::class, 'active'])->name('locker.active');
    Route::get('/locker/history', [LockerController::class, 'history'])->name('locker.history');
    Route::get('/locker/control', [LockerController::class, 'control'])->name('locker.control');
    Route::post('/locker/control/update/{id}', [LockerController::class, 'update_control'])->name('locker.control.update');
    Route::get('/locker/access', [LockerController::class, 'access'])->name('locker.access');
    Route::post('/locker/access/update/{id}', [LockerController::class, 'update_access'])->name('locker.access.update');
    Route::post('/locker/access/sync', [LockerController::class, 'sync_access'])->name('locker.access.sync');
    Route::post('/locker/open', [LockerController::class, 'open'])->name('locker.open');
    Route::post('/locker/force/{id}', [LockerController::class, 'force_unlock'])->name('locker.force');

    Route::get('/copier', [CopierController::class, 'index'])->name('copier.dashboard');
    Route::get('/copier/history', [CopierController::class, 'history'])->name('copier.history');
});

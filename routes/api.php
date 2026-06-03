<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AndonController;
use App\Http\Controllers\Api\CopierController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/andon/reset', [AndonController::class, 'reset'])->name('api.andon.reset');
Route::get('/andon/update', [AndonController::class, 'update'])->name('api.andon.update');

Route::post('/copier', [CopierController::class, 'store']);

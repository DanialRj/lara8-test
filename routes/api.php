<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
Use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\Auth\AuthController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Auth Route
Route::group(['prefix' => 'auth'], function() {
    // User OAuth Social Media Register & Login
    Route::group(['prefix' => 'oauth'], function() {
        Route::get('{driver}', [AuthController::class, 'redirectToProvider']);
        Route::get('{driver}/callback', [AuthController::class, 'handleProviderCallback']);
    });

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::group(['middleware' => ['auth:sanctum']], function() {
        Route::post('logout', [Authcontroller::class, 'logout']);
    });
});


// User Transaction Route
Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum']], function() {
    Route::get('me', [TransactionController::class, 'myInfo']);


    Route::get('position', [TransactionController::class, 'getAll']);
    Route::get('position/{id}', [TransactionController::class, 'show']);
    Route::post('position/create', [TransactionController::class, 'store']);
    Route::post('position/{id}/edit', [TransactionController::class, 'update']);
    Route::delete('position/{id}/delete', [TransactionController::class, 'destroy']);
});

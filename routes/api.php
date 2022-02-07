<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LevelController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\HistoryController;
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
Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);
Route::get('logout', [AuthController::class, 'signout']);

//Route::get('user-list', [UserController::class, 'index'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    //API Endpoint User Management
    Route::post("create-user", [UserController::class, "store"]);
    Route::get("list-users", [UserController::class, "index"]);
    Route::post("edit-user/{id}", [UserController::class, "update"]);
    Route::get("detail-user/{id}", [UserController::class, "show"]);
    Route::delete("delete-user/{id}", [UserController::class, "destroy"]);

    Route::get("list-user-level", [LevelController::class, "index"]);

    //API Endpoint payment
    Route::post("create-payment", [PaymentController::class, "store"]);
    Route::get("list-payment", [PaymentController::class, "index"]);
    Route::post("edit-payment/{id}", [PaymentController::class, "update"]);
    Route::get("detail-payment/{id}", [PaymentController::class, "show"]);
    Route::delete("delete-payment/{id}", [PaymentController::class, "destroy"]);

    //API History
    Route::post("create-history", [HistoryController::class, "store"]);
    Route::get("list-history", [HistoryController::class, "index"]);
    Route::get("list-history-by-payment/{id}", [HistoryController::class, "showByPaymentID"]);
    Route::get("total-saldo", [HistoryController::class, "getSaldo"]);
    Route::get("total-income", [HistoryController::class, "getIncome"]);
    Route::get("total-outcome", [HistoryController::class, "getOutcome"]);
    Route::post("update-history/{id}", [HistoryController::class, "update"]);
    Route::delete("delete-history/{id}", [HistoryController::class, "destroy"]);
 });

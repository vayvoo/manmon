<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ActionController;
use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\QuestionsController;
use App\Http\Controllers\Api\QuizzesController;
use App\Http\Controllers\Api\ResultsController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/currentUser', [AuthController::class, 'getUser']);
    Route::post('/setBalance', [AuthController::class, 'setBalance']);
    //Actions
    Route::get('/getActions', [ActionController::class, 'index']);
    Route::post('/addAction', [ActionController::class, 'update']);
    Route::delete('/deleteAction/{id}', [ActionController::class, 'destroy']);
});

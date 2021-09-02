<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\UserhastaskController;

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


Route::prefix('user')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::group(['middleware' => ['web']], function () {
        Route::get('google', [GoogleController::class, 'redirectToGoogle']);
        Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback']);
    });
    
    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh-token', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
        Route::post('/profile-picture', [AuthController::class, 'profilePicture']);
        Route::put('/update', [AuthController::class, 'update']);
        Route::delete('/delete', [AuthController::class, 'delete']);
    });
});

Route::prefix('task')->group(function(){
    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::post('/', [TaskController::class, 'store']);
        Route::get('/', [TaskController::class, 'index']);
        Route::get('/taskme', [TaskController::class, 'taskme']);
        Route::post('/update/{id}', [TaskController::class, 'update']);
        Route::delete('/delete/{id}', [TaskController::class, 'destroy']);
        Route::get('/tasktome/{task_id}', [UserhastaskController::class, 'tasktome']);
    });
});

Route::prefix('subtask')->group(function () {
    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::get('/{task_id}', [SubtaskController::class, 'index']);
        Route::get('/show/{id}', [SubtaskController::class, 'show']);
        Route::post('/', [SubtaskController::class, 'store']);
        Route::post('/update/{id}', [SubtaskController::class, 'update']);
        Route::delete('/delete/{id}', [SubtaskController::class, 'destroy']);
    });
});

Route::get('test', function() {
    Storage::disk('google')->put('test.txt', 'Hello World');
});
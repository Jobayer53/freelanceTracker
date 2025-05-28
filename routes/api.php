<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TimeLogController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login-error', [AuthController::class, 'login_error'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    //clients
    Route::get('/clients', [ClientController::class, 'index']);
    Route::post('/clients/store', [ClientController::class, 'store']);
    Route::get('/clients/show/{id}', [ClientController::class, 'show']);
    Route::post('/clients/update', [ClientController::class, 'update']);
    Route::get('/clients/delete/{id}', [ClientController::class, 'destroy']);
    //projects
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects/store', [ProjectController::class, 'store']);
    Route::get('/projects/show/{id}', [ProjectController::class, 'show']);
    Route::post('/projects/update', [ProjectController::class, 'update']);
    Route::get('/projects/delete/{id}', [ProjectController::class, 'destroy']);
    //timelogs
    Route::get('/time-logs', [TimeLogController::class, 'index']);
    Route::post('/time-logs', [TimeLogController::class, 'store']);
    Route::post('/time-logs/', [TimeLogController::class, 'update']);
    Route::get('/time-logs/{id}', [TimeLogController::class, 'destroy']);
    // log start end
    Route::post('/time-logs/start', [TimeLogController::class, 'start']);
    Route::post('/time-logs/end/{id}', [TimeLogController::class, 'end']);
    //view per day/week/custom
    Route::get('/time-logs-view', [TimeLogController::class, 'viewLogs']);
    //reportlogs
    Route::get('/reports/time-logs', [ReportController::class, 'timeLogSummary']);


});

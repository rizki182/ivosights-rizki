<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');

});

Route::get('/task', [TaskController::class, 'list']);
Route::get('/task/detail', [TaskController::class, 'detail']);
Route::get('/task/store', [TaskController::class, 'prepareStore']);
Route::post('/task/store', [TaskController::class, 'store']);
Route::get('/task/modify', [TaskController::class, 'prepareModify']);
Route::put('/task/modify', [TaskController::class, 'modify']);
Route::delete('/task/remove', [TaskController::class, 'remove']);
Route::put('/task/close', [TaskController::class, 'close']);
Route::get('/task/summary', [TaskController::class, 'summary']);

Route::get('/project', [ProjectController::class, 'list']);
Route::get('/project/detail', [ProjectController::class, 'detail']);
Route::post('/project/store', [ProjectController::class, 'store']);
Route::get('/project/modify', [ProjectController::class, 'prepareModify']);
Route::put('/project/modify', [ProjectController::class, 'modify']);
Route::delete('/project/remove', [ProjectController::class, 'remove']);
Route::get('/project/summary', [ProjectController::class, 'summary']);
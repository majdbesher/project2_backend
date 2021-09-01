<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\tripsController;
use App\Http\Controllers\reservationController;
use App\Http\Controllers\infoController;
use App\Http\Controllers\usersController;

//php artisan serve --host 192.168.43.200 --port 8000
//$out = new \Symfony\Component\Console\Output\ConsoleOutput();
//$out->writeln("Hello from Terminal");

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'info'

], function ($router) {
    Route::post('/provide_info', [infoController ::class, 'provide_info']);
    Route::post('/check_info', [infoController::class, 'check_info']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'reservation'
    
], function ($router) {
    Route::post('/res_at_trip', [reservationController::class, 'res_at_trip']);
    Route::post('/not_approved_res_at_trip', [reservationController::class, 'not_approved_res_at_trip']);
    Route::post('/reserve', [reservationController::class, 'reserve']);
    Route::post('/confirm_res', [reservationController::class, 'confirm_res']);
    Route::post('/customer_res', [reservationController::class, 'customer_res']);
    Route::post('/reserved_seats', [reservationController::class, 'reserved_seats']);
    Route::post('/del_reserve', [reservationController::class, 'del_reserve']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'trips'

], function ($router) {
    Route::post('/add_new_trip', [tripsController::class, 'add_new_trip']);
    Route::post('/ct', [tripsController::class, 'check_added_trips']);
    Route::post('/ct2', [tripsController::class, 'check_trips']);
    Route::get('/get_s_trips', [tripsController::class, 'get_s_trips']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/users', [AuthController::class, 'users_info']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'users'

], function ($router) {
    Route::get('/users', [usersController::class, 'users_info']);
});



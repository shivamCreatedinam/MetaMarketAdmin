<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
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

Route::get("/", [AuthController::class, "loginView"])->name("login");
Route::post("login", [AuthController::class, "loginPost"])->name("loginPost");


Route::group(['prefix' => 'admin',  'middleware' => 'auth:web'], function () {
    //All the routes that belongs to the group goes here
    Route::get('/dashboard',[HomeController::class,"dashboard"])->name("dashboard");
    Route::get('/user-list',[UserController::class,"userList"])->name("admin_user_list");

    Route::get("logout",[AuthController::class,"logout"])->name("admin_logout");
});

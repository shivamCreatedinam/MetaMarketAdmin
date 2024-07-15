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

Route::get("header", [HomeController::class, "printHeaders"]);

Route::group(['prefix' => 'admin',  'middleware' => 'auth:web'], function () {

    Route::get('/dashboard', [HomeController::class, "dashboard"])->name("dashboard");

    Route::group(['prefix' => 'user'], function () {
        Route::get('/user-list', [UserController::class, "userList"])->name("admin_user_list");
        Route::get('/user-view/{user_id}', [UserController::class, "userView"])->name("admin_user_view");
        Route::get('/user-edit/{user_id}', [UserController::class, "userEdit"])->name("admin_user_edit");
        Route::post('/user-update', [UserController::class, "userUpdate"])->name("admin_user_update");
        Route::post('/user-status-update', [UserController::class, "userStatusUpdate"])->name("admin_user_status_update");
    });

    Route::get("logout", [AuthController::class, "logout"])->name("admin_logout");
});

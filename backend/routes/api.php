<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\LoginController;

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
Route::middleware(['auth'])->group(function () {
    Route::apiResource('contacts', ContactController::class);
    Route::post('/contacts/{id}/update', [ContactController::class, 'update'])->name('contacts.update');
});


Route::post('/contacts/login', [LoginController::class, 'login'])->name('login');
Route::post('/contacts/register', [LoginController::class, 'register'])->name('register');

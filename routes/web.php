<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// роуты
Route::get('/', [IndexController::class, 'index'])
    ->name('index');
Route::get('/getevents/', [IndexController::class, 'setAppCalendar'])
    ->name('get.events');
Route::get('/privacy-policy', [IndexController::class, 'privacyPolicy'])
    ->name('privacy-policy');
Route::get('/user-agreement', [IndexController::class, 'userAgreement'])
    ->name('user-agreement');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])
    ->name('home');

Route::get('/add-calendar', [HomeController::class, 'addCalendar'])
    ->name('add-calendar');

Route::post('/add-calendar/save', [HomeController::class, 'saveCalendar'])
    ->name('calendar.save');

Route::get('/api/getevents', [Api::class, 'getCalendarEvents'])
    ->name('api.getevents');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');



Route::get('auth/facebook', [SocialController::class, 'facebookRedirect']);

Route::get('auth/facebook/callback', [SocialController::class, 'loginWithFacebook']);

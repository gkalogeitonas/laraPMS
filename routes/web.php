<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\BookingStatusController;
use App\Http\Controllers\BookingSourceController;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('homepage');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/calendar', [CalendarController::class, 'index'])->middleware(['auth', 'verified'])->name('calendar');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Room routes
    //Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    //Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('properties/{property}/rooms/create', [RoomController::class, 'create'])->name('properties.rooms.create');
    Route::post('properties/{property}/rooms', [RoomController::class, 'store'])->name('properties.rooms.store');
    Route::get('rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::patch('rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');


    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::patch('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    Route::post('/switch-tenant', [TenantSwitchController::class, 'switch']);

    Route::resource('customers', CustomerController::class);
    Route::get('/customer/search', [CustomerController::class, 'search'])->name('customers.search');

    Route::resource('bookings', BookingController::class);

    Route::get('/booking-statuses', [BookingStatusController::class, 'index'])->name('booking-statuses.index');
    Route::get('/booking-statuses/create', [BookingStatusController::class, 'create'])->name('booking-statuses.create');
    Route::get('/booking-statuses/{bookingStatus}/edit', [BookingStatusController::class, 'edit'])->name('booking-statuses.edit');
    Route::delete('/booking-statuses/{bookingStatus}', [BookingStatusController::class, 'destroy'])->name('booking-statuses.destroy');
    Route::patch('/booking-statuses/{bookingStatus}', [BookingStatusController::class, 'update'])->name('booking-statuses.update');
    Route::post('/booking-statuses', [BookingStatusController::class, 'store'])->name('booking-statuses.store');


    Route::get('/booking-sources', [BookingSourceController::class, 'index'])->name('booking-sources.index');
    Route::get('/booking-sources/create', [BookingSourceController::class, 'create'])->name('booking-sources.create');
    Route::get('/booking-sources/{bookingSource}/edit', [BookingSourceController::class, 'edit'])->name('booking-sources.edit');
    Route::delete('/booking-sources/{bookingSource}', [BookingSourceController::class, 'destroy'])->name('booking-sources.destroy');
    Route::patch('/booking-sources/{bookingSource}', [BookingSourceController::class, 'update'])->name('booking-sources.update');
    Route::post('/booking-sources', [BookingSourceController::class, 'store'])->name('booking-sources.store');


});


require __DIR__.'/auth.php';

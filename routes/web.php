<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TenantSwitchController;

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


Route::get('/calendar', function () {
    return Inertia::render('Calendar');
})->middleware(['auth', 'verified'])->name('calendar');

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

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::patch('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

});



require __DIR__.'/auth.php';

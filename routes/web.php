<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;

Route::get('/', [LocationController::class, 'index'])->name('location');
Route::post('/calculate', [LocationController::class, 'calculate'])->name('calculate');
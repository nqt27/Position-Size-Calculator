<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculationController;

Route::post('/calc', [CalculationController::class, 'apiCalc']);
Route::get('/history', [CalculationController::class, 'apiHistory']);

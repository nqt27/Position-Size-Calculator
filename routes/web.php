<?php

use Illuminate\Support\Facades\Route;



use App\Http\Controllers\CalculationController;

Route::get('/', [CalculationController::class, 'index'])->name('calculator.index');
Route::post('/calculator/calc', [CalculationController::class, 'calculate'])->name('calculator.calculate');
Route::delete('/calculations/{id}', [CalculationController::class, 'destroy'])->name('calculations.destroy');



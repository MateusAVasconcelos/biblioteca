<?php

use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::post('loans', [LoanController::class, 'store'])->name('loans.store');
    Route::put('loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
    Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
});

require __DIR__.'/settings.php';

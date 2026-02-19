<?php

use App\Http\Controllers\Api\LibraryController;
use Illuminate\Support\Facades\Route;

Route::get('/books', [LibraryController::class, 'books']);
Route::post('/loans', [LibraryController::class, 'storeLoan']);
Route::post('/returns/{loan_id}', [LibraryController::class, 'returnLoan']);

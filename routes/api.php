<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\BookController;

Route::middleware('hmac')->group(function () {
    Route::apiResource('books', BookController::class);
});

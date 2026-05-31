<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OfficeVNPT\QuangNgaiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/refresh', [AuthController::class, 'refresh']);

// Office VNPT External API Integration
Route::prefix('office-vnpt')->group(function () {
    Route::post('/quangngai/login', [QuangNgaiController::class, 'login']);
    Route::post('/quangngai/documents', [QuangNgaiController::class, 'getDocumentList']);
    Route::post('/quangngai/documents/sync', [QuangNgaiController::class, 'syncDocuments']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

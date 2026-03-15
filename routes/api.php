<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Employee\EmployeeController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas sem token
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']); // cadastro clinica + admin
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Precisa estar logado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user()->load('clinic');
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);

    /*
    |--------------------------------------------------------------------------
    | Rotas Verificadas (So acessa se clicou no e-mail)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['verified'])->group(function () {
        // Ex: Route::apiResource('pacientes', PatientController::class);
        // Ex: Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::middleware([EnsureUserIsAdmin::class])->group(function () {

            Route::patch('employee/{id}/restore', [EmployeeController::class, 'restore']);
            Route::apiResource('employee', EmployeeController::class)->only([
                'index', 'store', 'update', 'destroy'
            ]);
        });
    });
});

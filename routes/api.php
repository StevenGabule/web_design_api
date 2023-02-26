<?php

  use App\Http\Controllers\Auth\{ForgotPasswordController,
    LoginController,
    RegisterController,
    ResetPasswordController,
    VerificationController};
  use App\Http\Controllers\MeController;
  use Illuminate\Support\Facades\Route;

  /*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
  */

  /*
  |--------------------------------------------------------------------------
  | AUTHENTICATION API Routes
  |--------------------------------------------------------------------------
  */
  Route::prefix('auth')->group(function () {
    Route::middleware('guest:api')->group(function () {
      Route::post('/register', [RegisterController::class, 'register']);
      Route::post('/verification/verify', [VerificationController::class, 'verify'])->name('verification.verify');
      Route::post('/verification/resend', [VerificationController::class, 'resend'])->name('verification.resend');
      Route::post('/login', [LoginController::class, 'login']);
      Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
      Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');
    });

    Route::middleware('auth:api')->group(function () {
      Route::prefix('user')->group(function () {
        Route::get('/me', [MeController::class, 'currentUser']);
        Route::delete('/logout', [MeController::class, 'logout']);
      });
    });
  });



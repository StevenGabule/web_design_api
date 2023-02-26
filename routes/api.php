<?php

  use App\Http\Controllers\Designs\DesignController;
  use App\Http\Controllers\Designs\UploadController;
  use App\Http\Controllers\User\MeController;
  use App\Http\Controllers\Auth\{ForgotPasswordController,
    LoginController,
    RegisterController,
    ResetPasswordController,
    VerificationController};
  use Illuminate\Support\Facades\Route;

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
  });

  Route::middleware('auth:api')->group(function () {
    Route::prefix('user')->group(function () {
      Route::get('/me', [MeController::class, 'currentUser']);
      Route::delete('/logout', [MeController::class, 'logout']);
    });

    Route::prefix('design')->group(function () {
      Route::post('/upload', [UploadController::class, 'upload'])->name('design.upload');
      Route::put('/update/{design}', [DesignController::class, 'update'])->name('design.update');
      Route::delete('/destroy/{design}', [DesignController::class, 'destroy'])->name('design.destroy');
      Route::post('/restore/{design}', [DesignController::class, 'restore'])->name('design.restore');
      Route::delete('/force-delete/{design}', [DesignController::class, 'forceDelete'])->name('design.force_delete');
    });
  });



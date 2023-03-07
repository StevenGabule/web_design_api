<?php

  use Illuminate\Support\Facades\Route;
  use App\Http\Controllers\Chats\ChatController;
  use App\Http\Controllers\Teams\{InvitationController, TeamController};
  use App\Http\Controllers\UserController;
  use App\Http\Controllers\Designs\{CommentController, DesignController, UploadController};
  use App\Http\Controllers\User\MeController;
  use App\Http\Controllers\Auth\{ForgotPasswordController, LoginController, RegisterController, ResetPasswordController, VerificationController};

  Route::prefix('designs')->group(function () {
    Route::get('/', [DesignController::class, 'index']);
    Route::get('/{id}', [DesignController::class, 'findDesign']);
    Route::get('/slug/{slug}', [DesignController::class, 'findBySlug']);
  });

  Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{username}', [UserController::class, 'findByUsername']);
    Route::get('/{id}/designs', [UserController::class, 'getForUser']);
  });

  Route::prefix('teams')->group(function () {
    Route::get('/slug/{slug}', [TeamController::class, 'findBySlug']);
    Route::get('/{id}/designs', [DesignController::class, 'getForTeam']);
  });

  Route::prefix('search')->group(function () {
    Route::get('/designs', [DesignController::class, 'search']);
    Route::get('/designers', [UserController::class, 'search']);
  });


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

      // like and unliked
      Route::post('/{id}/like', [DesignController::class, 'like']);
      Route::post('/{id}/liked', [DesignController::class, 'checkIfUserHasLiked']);
    });

    Route::prefix('comment')->group(function () {
      Route::post('/store/{designId}', [CommentController::class, 'store'])->name('comment.store');
      Route::put('/update/{commentId}', [CommentController::class, 'update'])->name('comment.update');
      Route::delete('/destroy/{commentId}', [CommentController::class, 'destroy'])->name('comment.destroy');
      Route::post('/restore/{commentId}', [CommentController::class, 'restore'])->name('comment.restore');
      Route::delete('/force-delete/{commentId}', [CommentController::class, 'forceDelete'])->name('comment.force_delete');
    });

    Route::prefix('teams')->group(function () {
      Route::post('/', [TeamController::class, 'store']);
      Route::get('/{id}', [TeamController::class, 'findById']);
      Route::put('/{id}', [TeamController::class, 'update']);
      Route::delete('/{id}', [TeamController::class, 'destroy']);
      Route::get('/users', [TeamController::class, 'fetchUserTeams']);
    });

    Route::prefix('invitations')->group(function () {
      Route::post('/{teamId}', [InvitationController::class, 'invite']);
      Route::post('/{id}/resend', [InvitationController::class, 'resend']);
      Route::post('/{id}/respond', [InvitationController::class, 'respond']);
      Route::delete('/{id}', [InvitationController::class, 'destroy']);
    });

    Route::prefix('chats')->group(function () {
      Route::post('/', [ChatController::class, 'sendMessage']);
      Route::get('/', [ChatController::class, 'getUserChats']);
      Route::get('/{id}/messages', [ChatController::class, 'getChatMessages']);
      Route::put('/{id}/markAsRead', [ChatController::class, 'markAsRead']);
    });

    Route::delete('messages/{id}', [ChatController::class, 'destroyMessage']);

  });



<?php

  namespace App\Http\Controllers;

  use Illuminate\Http\JsonResponse;
  use Laravel\Passport\{RefreshTokenRepository, TokenRepository};

  class MeController extends Controller
  {
    public function currentUser(): JsonResponse
    {
      return response()->json(['user' => request()->user()]);
    }

    public function logout(): JsonResponse
    {
      $tokenId = request()->user()->token()->id;
      $tokenRepository = app(TokenRepository::class);
      $refreshTokenRepository = app(RefreshTokenRepository::class);
      $tokenRepository->revokeAccessToken($tokenId);
      $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
      return response()->json([], 204);
    }
  }

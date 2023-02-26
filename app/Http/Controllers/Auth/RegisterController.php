<?php

  namespace App\Http\Controllers\Auth;

  use App\Http\Controllers\Controller;
  use App\Http\Requests\Auth\RegisterRequest;
  use Illuminate\Http\JsonResponse;

  class RegisterController extends Controller
  {
    public function register(RegisterRequest $request): JsonResponse
    {
      if ($request->wantsJson()) {
        $request->store();



        return response()->json([
          'success' => true,
          'message' => 'User was successfully registered.'
        ], 201);
      }
    }
  }

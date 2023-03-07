<?php

  namespace App\Http\Controllers\Auth;

  use App\Http\Controllers\Controller;
  use App\Http\Requests\Auth\LoginRequest;
  use App\Models\Traits\ResponseReturn;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Validation\ValidationException;

  class LoginController extends Controller
  {
    use ResponseReturn;
    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
      if ($request->wantsJson()) {
        $request->authenticate();
        $user = Auth::user();
        $data['token'] = $user->createToken('MyApp')->accessToken;
        $data['name'] = $user->name();
        return $this->sendSuccessResponse($data, 'Successfully added');
      }
    }
  }

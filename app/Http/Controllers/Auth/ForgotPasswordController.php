<?php

  namespace App\Http\Controllers\Auth;

  use App\Http\Controllers\Controller;
  use Illuminate\Validation\ValidationException;
  use Illuminate\Http\{JsonResponse, Request, Response};
  use Illuminate\Support\Facades\Password;

  class ForgotPasswordController extends Controller
  {
    /**
     * @throws ValidationException
     */
    public function forgotPassword(Request $request): JsonResponse
    {
      if ($request->wantsJson()) {
        // ** validate email
        $this->validate($request, ['email' => 'required|email']);

        // ** send email for resetting the password
        $status = Password::sendResetLink($request->only('email'));

        // ** check if the email notification is completed
        if ($status === Password::RESET_LINK_SENT) {
          return response()->json(['message' => __($status)]);
        }

        // ** return error if something goes up
        return response()->json(['message' => __($status)]);
      }
      return response()->json(['message' => 'Something went wrong!'], Response::HTTP_BAD_REQUEST);
    }
  }

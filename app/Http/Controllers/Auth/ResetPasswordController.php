<?php

  namespace App\Http\Controllers\Auth;

  use App\Http\Controllers\Controller;
  use Illuminate\Auth\Events\PasswordReset;
  use Illuminate\Contracts\Auth\{CanResetPassword, PasswordBroker};
  use JetBrains\PhpStorm\ArrayShape;
  use Illuminate\Http\{JsonResponse, Request, Response};
  use Illuminate\Support\Facades\{Hash,Password};
  use Illuminate\Support\Str;

  class ResetPasswordController extends Controller
  {
    public function reset(Request $request): JsonResponse
    {
      if ($request->wantsJson()) {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
          $this->credentials($request),
          function ($user, $password) {
            $this->resetPassword($user, $password);
          }
        );

        // If the password was successfully reset, we will give a response the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response === Password::PASSWORD_RESET ?
          response()->json(['message' => __($response)]) :
          response()->json(['message' => __($response)], 400);
      }
      return response()->json(['message' => 'Something goes wrong!'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Set the user's password.
     * @param CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function setUserPassword(CanResetPassword $user, string $password)
    {
      $user->password = Hash::make($password);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    #[ArrayShape(['token' => "string", 'email' => "string", 'password' => "string", 'confirmed_password' => "string"])] protected function rules(): array
    {
      return [
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8',
        'confirmed_password' => 'required|same:password',
      ];
    }

    /**
     * Get the password reset validation error messages.
     * @return array
     */
    protected function validationErrorMessages(): array
    {
      return [];
    }

    /**
     * Get the password reset credentials from the request.
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request): array
    {
      return $request->only('email', 'password', 'password_confirmation', 'token');
    }

    /**
     * Reset the given user's password.
     *
     * @param CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword(CanResetPassword $user, string $password)
    {
      $this->setUserPassword($user, $password);
      $user->setRememberToken(Str::random(60));
      $user->save();
      event(new PasswordReset($user));
    }

    /**
     * Get the broker to be used during password reset.
     * @return PasswordBroker
     */
    public function broker(): PasswordBroker
    {
      return Password::broker();
    }
  }

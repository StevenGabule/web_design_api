<?php

  namespace App\Http\Controllers\Auth;

  use App\Http\Controllers\Controller;
  use App\Models\User;
  use Illuminate\Auth\Events\Verified;
  use Illuminate\Support\Facades\URL;
  use Illuminate\Validation\ValidationException;
  use Illuminate\Http\{JsonResponse, Request, Response};

  class VerificationController extends Controller
  {
    public function __construct()
    {
//      $this->middleware('signed')->only('verify');
//      $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request): JsonResponse
    {
      $id = $request->get('id');
      $hash = $request->get('hash');

      // check if the url is valid signed url
      if (!URL::hasValidSignature($request)) {
        return response()->json(['errors' => [
          'message' => 'Invalid verification link'
        ]], 422);
      }

      $user = User::where('id', $id)->first();
      if (!$user) return response()->json(['message' => 'Account is not found. Kindly check your email.'], Response::HTTP_BAD_REQUEST);
      if (!hash_equals((string) $id, (string) $user->getKey())) return response()->json(['message' => 'Something goes wrong!'], Response::HTTP_BAD_REQUEST);
      if (!hash_equals((string) $hash,sha1($user->getEmailForVerification()))) return response()->json(['message' => 'Something goes wrong!'], Response::HTTP_BAD_REQUEST);
      if ($user->hasVerifiedEmail()) return response()->json(['message' => 'Email is already verified.'], Response::HTTP_BAD_REQUEST);
      $user->markEmailAsVerified();
      event(new Verified($user));
      return response()->json(['message' => 'You successfully verified your account! Thank you for complying.'], Response::HTTP_OK);
    }

    /**
     * @throws ValidationException
     */
    public function resend(Request $request): JsonResponse
    {
      $this->validate($request, [
        'email' => ['email', 'required'],
      ]);

      $user = User::where('email', $request->get('email'))->first();

      if (!$user) {
        return response()->json(['errors' => [
          'email' => 'No user could be found with this email address'
        ]], 422);
      }

      if ($user->hasVerifiedEmail()) {
        return response()->json(['errors' => [
          'message' => 'Email address already verified'
        ]], 422);
      }

      $user->sendEmailVerificationNotification();
      return response()->json(['success' => true], 201);
    }
  }

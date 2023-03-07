<?php

namespace App\Http\Requests\Auth;

use App\Models\Traits\ResponseReturn;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use JetBrains\PhpStorm\ArrayShape;

class LoginRequest extends FormRequest
{
  use ResponseReturn;
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, mixed>
     */
    #[ArrayShape(['email' => "string", 'current_password' => "string"])] public function rules(): array
    {
        return [
          'email' => 'required',
          'current_password' => 'required',
        ];
    }

  /**
   * @throws ValidationException
   */
  public function authenticate()
  {
    $this->ensureIsNotRateLimited();
    if (!Auth::attempt([
      'email' => request('email'),
      'password' => request('current_password')
    ], $this->boolean('remember'))) {
      RateLimiter::hit($this->throttleKey());
      throw ValidationException::withMessages([
        'email' => __('auth.failed'),
      ]);
    }
  }

  /**
   * Ensure the login request is not rate limited.
   * @return void
   * @throws ValidationException
   */
  public function ensureIsNotRateLimited()
  {
    if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
      return;
    }
    event(new Lockout($this));
    $seconds = RateLimiter::availableIn($this->throttleKey());
    throw ValidationException::withMessages([
      'email' => trans('auth.throttle', [
        'seconds' => $seconds,
        'minutes' => ceil($seconds / 60),
      ]),
    ]);
  }

  /**
   * Get the rate limiting throttle key for the request.
   * @return string
   */
  public function throttleKey(): string
  {
    return $this->ip();
  }
}

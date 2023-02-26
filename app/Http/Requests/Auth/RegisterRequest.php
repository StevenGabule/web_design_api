<?php

  namespace App\Http\Requests\Auth;

  use App\Models\User;
  use Illuminate\Auth\Events\Registered;
  use Illuminate\Foundation\Http\FormRequest;
  use JetBrains\PhpStorm\ArrayShape;

  class RegisterRequest extends FormRequest
  {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
      return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['username' => "string", 'first_name' => "string", 'last_name' => "string", 'email' => "string", 'phone_number' => "string", 'current_password' => "string", 'confirm_password' => "string"])] public function rules(): array
    {
      return [
        'username' => 'required|unique:users,username',
        'first_name' => 'required|min:3',
        'last_name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'phone_number' => 'required|unique:users,phone_number',
        'current_password' => 'required|min:8',
        'confirm_password' => 'required|same:current_password'
      ];
    }

    public function store()
    {
      $user = User::create([
        'username' => request('username'),
        'first_name' => request('first_name'),
        'last_name' => request('last_name'),
        'email' => request('email'),
        'password' => bcrypt(request('current_password')),
        'phone_number' => request('phone_number'),
      ]);

      event(new Registered($user));
    }
  }

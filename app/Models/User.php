<?php

  namespace App\Models;

  use Illuminate\Contracts\Auth\MustVerifyEmail;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  use Illuminate\Foundation\Auth\User as Authenticatable;
  use Illuminate\Notifications\Notifiable;
  use Laravel\Passport\HasApiTokens;

  /**
   * @method static create(array $array1)
   * @method static $this createToken(string $string1)
   * @property string $first_name
   * @property string $last_name
   * @property string $password
   * @property integer $id
   */
  class User extends Authenticatable implements MustVerifyEmail
  {
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
      'password',
      'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
      'email_verified_at' => 'datetime',
    ];

    public function name(): string
    {
      return "{$this->first_name} {$this->last_name}";
    }

    public function designs(): HasMany
    {
      return $this->hasMany(Design::class);
    }
  }

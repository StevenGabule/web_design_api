<?php

  namespace App\Models;

  use Illuminate\Contracts\Auth\MustVerifyEmail;
  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
   * @property string $email
   * @property integer $id
   */
  class User extends Authenticatable implements MustVerifyEmail
  {
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $appends = ['photo_url'];

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
      'available_to_hire' => 'boolean'
    ];

    public function getPhotoUrlAttribute(): string
    {
      return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
    }

    public function name(): string
    {
      return "{$this->first_name} {$this->last_name}";
    }

    public function designs(): HasMany
    {
      return $this->hasMany(Design::class);
    }

    public function teams(): BelongsToMany
    {
      return $this->belongsToMany(Team::class)->withTimestamps();
    }

    public function ownedTeams(): BelongsToMany
    {
      return $this->teams()->where('owner_id', $this->id);
    }

    public function isOwnerOfTeam($team): bool
    {
      return (bool)$this->teams()
        ->where('id', $team->id)
        ->orWhere('owner_id', $team->id)
        ->count();
    }

    public function invitations(): HasMany
    {
      return $this->hasMany(Invitation::class, 'recipient_email', 'email');
    }

    public function chats(): BelongsToMany
    {
      return $this->belongsToMany(Chat::class, 'participants');
    }

    public function messages(): HasMany
    {
      return $this->hasMany(Message::class);
    }

    /**
     * @param int $user_id
     * @return mixed
     */
    public function getChatWithUser(int $user_id): mixed
    {
      return $this->chats()->whereHas('participants', function ($query) use ($user_id) {
        $query->where('user_id', $user_id);
      })->first();
    }

  }

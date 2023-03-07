<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
/**
 * @property string $name
 * @property int $id
 */
  class Team extends Model
  {
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
      parent::boot();

      // ** when team is created, add current user as team member
      static::created(function ($team) {
        $team->members()->attach(auth()->id());
      });

      static::deleting(function ($team) {
        $team->members()->sync([]);
      });
    }

    public function owner(): BelongsTo
    {
      return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
      return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function designs(): HasMany
    {
      return $this->hasMany(Design::class);
    }

    public function hasUser(User $user): bool
    {
      return (bool)$this->members()
        ->where('user_id', $user->id)
        ->first();
    }

    public function invitations(): HasMany
    {
      return $this->hasMany(Invitation::class);
    }

    public function hasPendingInvite(string $email): bool
    {
      return (bool)$this->invitations()
        ->where('recipient_email', $email)
        ->count();
    }

  }

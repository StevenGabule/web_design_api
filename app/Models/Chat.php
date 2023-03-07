<?php

  namespace App\Models;

  use Carbon\Carbon;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;
  use Illuminate\Database\Eloquent\Relations\HasMany;

  class Chat extends Model
  {
    use HasFactory;

    /**
     * @return BelongsToMany
     */
    public function participants(): BelongsToMany
    {
      return $this->belongsToMany(User::class,'participants');
    }

    public function messages(): HasMany
    {
      return $this->hasMany(Message::class);
    }

    public function getLatestMessageAttribute()
    {
      return $this->messages()->latest()->first();
    }

    public function isUnreadForUser($userId): bool
    {
      return (bool)$this->messages()
        ->whereNull('last_read')
        ->where('user_id', '<>', $userId)
        ->count();
    }

    public function markAsReadForUser($userId)
    {
      $this->messages()
        ->whereNull('last_read')
        ->where('user_id', '<>', $userId)
        ->update(['last_read' => Carbon::now()]);
    }

  }

<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne};
/**
 * @property integer $sender_id
 * @property string $recipient_email
 */
  class Invitation extends Model
  {
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
      return $this->belongsTo(Team::class);
    }

    /**
     * @return HasOne
     */
    public function recipient(): HasOne
    {
      return $this->hasOne(User::class, 'email', 'recipient_email');
    }

    /**
     * @return HasOne
     */
    public function sender(): HasOne
    {
      return $this->hasOne(User::class, 'id', 'sender_id');
    }
  }

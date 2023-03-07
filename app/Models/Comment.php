<?php

  namespace App\Models;

  use App\Models\Traits\Likeable;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo, Relations\MorphTo, SoftDeletes};

  /**
   * @property integer $user_id
   */
  class Comment extends Model
  {
    use HasFactory, SoftDeletes, Likeable;

    protected $guarded = [];

    public function commentable(): MorphTo
    {
      return $this->morphTo();
    }

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }
  }

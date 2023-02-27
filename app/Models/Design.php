<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\{Model, Relations\MorphMany, SoftDeletes};
  use Illuminate\Database\Eloquent\Relations\BelongsTo;

  /**
   * @property string $disk
   * @property string $image
   * @property integer $user_id
   * @property integer $id
   * @property boolean $upload_success
   */
  class Design extends Model
  {
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }

    public function comments(): MorphMany
    {
      return $this->morphMany(Comment::class, 'commentable')
        ->orderBy('created_at', 'asc');
    }
  }

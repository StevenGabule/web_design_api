<?php

  namespace App\Models;

  use App\Models\Traits\Likeable;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Support\Facades\Storage;
  use JetBrains\PhpStorm\ArrayShape;
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
    use HasFactory, SoftDeletes, Likeable;

    protected $guarded = [];

    protected $casts = [
      'is_live' => 'boolean',
      'upload_successful' => 'boolean',
      'close_to_comments' => 'boolean',
    ];

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }

    public function comments(): MorphMany
    {
      return $this->morphMany(Comment::class, 'commentable')
        ->orderBy('created_at', 'asc');
    }

    public function team(): BelongsTo
    {
      return $this->belongsTo(Team::class);
    }

    #[ArrayShape(['thumbnail' => "string", 'large' => "string", 'original' => "string"])]
    public function getImageAttribute(): array
    {
      return [
        'thumbnail' => $this->getImagePath('thumbnail'),
        'large' => $this->getImagePath('large'),
        'original' => $this->getImagePath('original'),
      ];
    }

    public function getImagePath($size): string
    {
      return Storage::disk($this->disk)
        ->url("uploads/designs/{$size}/{$this->image}");
    }
  }

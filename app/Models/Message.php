<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\SoftDeletes;


  class Message extends Model
  {
    use HasFactory, SoftDeletes;

    protected $table = 'messages';

    protected $touches = ['chat'];

    protected $guarded = [];

    public function getBodyAttribute($value): ?string
    {
      if(!auth()->check()) return null;

      if ($this->trashed()) {
        return auth()->id() == $this->sender->id ? 'You deleted this message.' : "{$this->sender->name} deleted this message.";
      }
      return $value;
    }

    public function chat(): BelongsTo
    {
      return $this->belongsTo(Chat::class);
    }

    public function sender(): BelongsTo
    {
      return $this->belongsTo(User::class, 'user_id');
    }


  }

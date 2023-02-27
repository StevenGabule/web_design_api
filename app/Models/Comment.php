<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};

/**
 * @property integer $user_id
 */
class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
}

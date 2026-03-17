<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Social_post extends Model
{
    protected $fillable = ['user_id', 'title', 'content', 'media_path'];

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function comments():MorphMany{
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function likes():MorphMany{
        return $this->morphMany(Like::class, 'likeable');
    }
}

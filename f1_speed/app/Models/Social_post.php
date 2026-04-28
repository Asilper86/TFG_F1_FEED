<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Social_post extends Model
{
    protected $fillable = ['user_id', 'title', 'content', 'media_path', 'lap_id', 'original_post_id'];

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function lap():BelongsTo{
        return $this->belongsTo(Lap::class);
    }

    public function originalPost():BelongsTo{
        return $this->belongsTo(Social_post::class, 'original_post_id');
    }

    public function hashtags(){
        return $this->belongsToMany(Hashtag::class, 'hashtag_social_post');
    }

    public function comments():MorphMany{
        return $this->morphMany(Comment::class, 'commentable');
    }
    public function likes():MorphMany{
        return $this->morphMany(Like::class, 'likeable');
    }
}

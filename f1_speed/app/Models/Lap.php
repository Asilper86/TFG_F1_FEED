<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lap extends Model
{
    protected $fillable = ['session_id', 'lap_time', 'sector_1', 'sector_2', 'sector_3'];

    public function session():BelongsTo{
        return $this->belongsTo(Racing_session::class, 'racing_sessions_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function telemetryLogs():HasMany{
        return $this->hasMany(Telemetry_log::class);
    }
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}

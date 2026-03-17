<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Racing_session extends Model
{
    protected $fillable = ['user_id', 'sim_key', 'track_id', 'car_id', 'weather_conditions'];

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function laps():HasMany{
        return $this->hasMany(Lap::class);
    }
}

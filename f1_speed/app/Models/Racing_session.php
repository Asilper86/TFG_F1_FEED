<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Racing_session extends Model
{
    protected $fillable = ['user_id','last_status_json', 'sim_key', 'track_id', 'car_id', 'weather', 'weather_conditions', 'setup_json', 'is_active'];

    protected $casts = [
        'setup_json' => 'array',
        'is_active' => 'boolean',
        'last_status_json' => 'array',
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function laps():HasMany{
        return $this->hasMany(Lap::class);
    }
}

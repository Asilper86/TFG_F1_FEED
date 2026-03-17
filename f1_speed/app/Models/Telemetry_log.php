<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Telemetry_log extends Model
{
    protected $fillable = ['lap_id', 'telemetry_json'];

    protected $casts = [
        'telemetry_json' => 'array'
    ];

    public function lap(): BelongsTo{
        return $this->belongsTo(Lap::class);
    }
}

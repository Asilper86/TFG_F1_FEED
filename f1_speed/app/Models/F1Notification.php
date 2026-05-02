<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class F1Notification extends Model
{
    protected $table = 'f1_notifications';

    protected $fillable = ['user_id', 'actor_id', 'type', 'notifiable_id', 'notifiable_type', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }
}

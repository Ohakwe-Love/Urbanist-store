<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected $appends = [
        'latest_activity_at',
    ];

    public function replies()
    {
        return $this->hasMany(ContactMessageReply::class)->oldest();
    }

    public function getLatestActivityAtAttribute(): Carbon
    {
        $replyTimestamp = $this->getAttribute('replies_max_created_at');

        if ($replyTimestamp instanceof Carbon) {
            return $replyTimestamp;
        }

        if (is_string($replyTimestamp) && $replyTimestamp !== '') {
            return Carbon::parse($replyTimestamp);
        }

        if ($this->relationLoaded('replies') && $this->replies->isNotEmpty()) {
            return $this->replies->max('created_at');
        }

        return $this->created_at;
    }
}

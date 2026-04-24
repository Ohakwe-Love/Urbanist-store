<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationEmailVerification extends Model
{
    protected $fillable = [
        'email',
        'token',
        'expires_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeUnexpired($query)
    {
        return $query->where('expires_at', '>', now());
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GmailToken extends Model
{
    protected $fillable = [
        'email',
        'access_token',
        'refresh_token',
        'expires_in',
        'token_created_at',
    ];

    protected function casts(): array
    {
        return [
            'token_created_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        if (! $this->token_created_at || ! $this->expires_in) {
            return true;
        }

        return now()->timestamp > ($this->token_created_at->timestamp + $this->expires_in - 60);
    }
}

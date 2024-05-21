<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class WebhookLog extends Model
{
    use HasFactory;

    protected $casts = [
        'payload' => 'array',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public static function add(string $name, Request $payload, string $identifier = null): self
    {
        return static::create([
            'name' => $name,
            'payload' => $payload,
            'service_identifier' => $identifier,
        ]);
    }
}

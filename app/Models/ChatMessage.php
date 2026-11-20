<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ChatMessage extends Model
{
    protected $fillable = [
        'session_id',
        'role',
        'content',
        'intent',
        'model',
    ];
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends BaseModel
{
    use HasFactory;

    protected $fillable = ['chat_id', 'body', 'user_id', 'created_at', 'updated_at'];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}

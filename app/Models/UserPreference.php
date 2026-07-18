<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = ['focus_minutes', 'short_break_minutes', 'long_break_minutes', 'focus_tone', 'break_tone', 'browser_notifications', 'zen_mode'];

    protected function casts(): array
    {
        return [
            'browser_notifications' => 'boolean',
            'zen_mode' => 'boolean',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

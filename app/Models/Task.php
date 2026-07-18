<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'due_at', 'priority', 'session_minutes',
        'notify_on_end', 'five_minute_warning', 'category_id', 'status', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'immutable_datetime',
            'completed_at' => 'immutable_datetime',
            'notify_on_end' => 'boolean',
            'five_minute_warning' => 'boolean',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function sessions(): HasMany { return $this->hasMany(FocusSession::class); }
    public function isCompleted(): bool { return $this->status === 'completed'; }
}

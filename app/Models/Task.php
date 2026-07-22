<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $category_id
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $due_at
 * @property string $priority
 * @property int $session_minutes
 * @property bool $notify_on_end
 * @property bool $five_minute_warning
 * @property string $status
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Category|null $category
 * @property-read Collection<int, FocusSession> $sessions
 * @property-read int|null $sessions_sum_duration_seconds
 */
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
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

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<FocusSession, $this>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(FocusSession::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}

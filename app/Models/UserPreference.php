<?php

namespace App\Models;

use Database\Factories\UserPreferenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $focus_minutes
 * @property int $short_break_minutes
 * @property int $long_break_minutes
 * @property string $focus_tone
 * @property string $break_tone
 * @property bool $browser_notifications
 * @property bool $zen_mode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class UserPreference extends Model
{
    /** @use HasFactory<UserPreferenceFactory> */
    use HasFactory;

    protected $fillable = ['focus_minutes', 'short_break_minutes', 'long_break_minutes', 'focus_tone', 'break_tone', 'browser_notifications', 'zen_mode'];

    protected function casts(): array
    {
        return [
            'browser_notifications' => 'boolean',
            'zen_mode' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

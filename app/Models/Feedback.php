<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feedback extends Model
{
    public const CATEGORIES = [
        'general' => 'General Feedback',
        'bug_report' => 'Bug Report',
        'feature_request' => 'Feature Request',
        'service_quality' => 'Service Quality',
        'usability' => 'Usability / UX',
        'content_request' => 'Content Request',
    ];

    protected $table = 'feedback';
    protected $fillable = [
        'user_id',
        'title',
        'parent_id',
        'type',
        'category',
        'course',
        'role',
        'is_anonymous',
        'status',
        'message',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public static function categoryOptions(): array
    {
        return self::CATEGORIES;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function scopeThreads($query)
    {
        return $query->where('type', 'thread')->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->where('type', 'reply');
    }
}

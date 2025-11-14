<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Schema;

class SubmissionComment extends Model
{
    public const ST_OPEN    = 'open';
    public const ST_APPLIED = 'applied';

    public const LV_MUST_FIX   = 'must_fix';
    public const LV_SHOULD_FIX = 'should_fix';
    public const LV_NIT        = 'nit';

    protected $table = 'submission_comments';
    protected $guarded = [];

    protected $casts = [
        'resolved_at'             => 'datetime',
        'resolved_by_author_at'   => 'datetime',
        'verified_by_reviewer_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $m) { $m->submission?->recomputeStatus(); });
        static::deleted(function (self $m) { $m->submission?->recomputeStatus(); });
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(SubmissionSection::class, 'section_id');
    }

    public function user(): BelongsTo
    {
        $fk = Schema::hasColumn($this->table, 'user_id') ? 'user_id' : 'author_id';
        return $this->belongsTo(User::class, $fk);
    }

    public function author(): BelongsTo
    {
        $fk = Schema::hasColumn($this->table, 'author_id') ? 'author_id' : 'user_id';
        return $this->belongsTo(User::class, $fk);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolver_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeOpen($q)
    {
        return $q->where('status', self::ST_OPEN);
    }

    public function scopeBlocking($q)
    {
        return $q->where('level', self::LV_MUST_FIX)->where('status', self::ST_OPEN);
    }

    public function scopeForSubmission($q, int $submissionId)
    {
        return $q->where('submission_id', $submissionId);
    }

    public function scopeByLevel($q, string $level)
    {
        return $q->where('level', $level);
    }

    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attr) => $attr['excerpt'] ?? $attr['quote'] ?? null,
            set: fn ($value) => Schema::hasColumn($this->table, 'excerpt')
                ? ['excerpt' => $value]
                : ['quote' => $value],
        );
    }

    protected function suggestedText(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attr) => $attr['suggested_text'] ?? $attr['note'] ?? null,
            set: fn ($value) => Schema::hasColumn($this->table, 'suggested_text')
                ? ['suggested_text' => $value]
                : ['note' => $value],
        );
    }
}

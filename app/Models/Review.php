<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public const ST_ASSIGNED = 'atribuida';
    public const ST_IN_REVIEW = 'em_revisao';
    public const ST_REV_REQ = 'revisao_solicitada';
    public const ST_OPINION_SENT = 'parecer_enviado';

    protected $table = 'reviews';

    protected $fillable = [
        'submission_id',
        'reviewer_id',
        'status',
        'assigned_at',
        'due_at',
        'requested_corrections_at',
        'submitted_opinion_at',
        'recommendation',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_at' => 'datetime',
        'requested_corrections_at' => 'datetime',
        'submitted_opinion_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function scopeOpen($q)
    {
        return $q->whereIn('status', [self::ST_ASSIGNED, self::ST_IN_REVIEW, self::ST_REV_REQ]);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::ST_ASSIGNED, self::ST_IN_REVIEW, self::ST_REV_REQ], true);
    }

    public function isOpinionSent(): bool
    {
        return $this->status === self::ST_OPINION_SENT;
    }

    public function scopeForReviewer($q, int $userId)
    {
        return $q->where('reviewer_id', $userId);
    }
}

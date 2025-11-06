<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
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
    ];

    protected $casts = [
        'assigned_at'              => 'datetime',
        'due_at'                   => 'datetime',
        'requested_corrections_at' => 'datetime',
        'submitted_opinion_at'     => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}

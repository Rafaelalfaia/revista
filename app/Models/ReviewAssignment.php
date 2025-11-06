<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewAssignment extends Model
{
    protected $fillable = [
        'submission_id','reviewer_id','coordinator_id','status','assigned_at','due_at'
    ];
    protected $casts = ['assigned_at'=>'datetime','due_at'=>'datetime'];

    public function submission(): BelongsTo { return $this->belongsTo(Submission::class); }
    public function reviewer(): BelongsTo   { return $this->belongsTo(User::class, 'reviewer_id'); }
    public function coordinator(): BelongsTo{ return $this->belongsTo(User::class, 'coordinator_id'); }
}

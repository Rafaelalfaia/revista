<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionAsset extends Model
{
    protected $fillable = [
        'submission_id',
        'section_id',
        'type',
        'file_path',
        'caption',
        'source',
        'order',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(SubmissionSection::class, 'section_id');
    }
}

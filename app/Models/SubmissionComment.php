<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionComment extends Model
{
    protected $table = 'submission_comments';

    protected $fillable = [
    'submission_id','section_id','parent_id',
    'user_id',
    'type','level',
    'excerpt','quote',
    'suggested_text','note',
    'status',
    'resolver_id','resolved_at',
    'resolved_by_author_at','verified_by_reviewer_at',
    ];


    protected $casts = [
        'resolved_at'             => 'datetime',
        'resolved_by_author_at'   => 'datetime',
        'verified_by_reviewer_at' => 'datetime',
    ];

    // Relações
    public function submission()
    {
        return $this->belongsTo(\App\Models\Submission::class);
    }

    public function section()
    {
        return $this->belongsTo(\App\Models\SubmissionSection::class, 'section_id');
    }

    // Alias "author" continua funcionando nos Blades, mas usando user_id
    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function resolver()
    {
        return $this->belongsTo(\App\Models\User::class, 'resolver_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Scopes úteis
    public function scopeOpen($q)
    {
        return $q->where('status', 'open');
    }

    public function scopeBlocking($q)
    {
        return $q->where('level', 'must_fix')->where('status', 'open');
    }
}

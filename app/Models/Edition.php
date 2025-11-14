<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Edition extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'profile_photo_path',
        'profile_photo_disk',
        'cover_photo_path',
        'cover_photo_disk',
        'release_date',
        'published_at',
        'meta',
    ];

    protected $casts = [
        'release_date' => 'date',
        'published_at' => 'datetime',
        'meta' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $edition) {
            if (empty($edition->slug) && !empty($edition->title)) {
                $edition->slug = Str::slug($edition->title).'-'.Str::random(6);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function submissions(): BelongsToMany
    {
        return $this->belongsToMany(Submission::class, 'edition_submission')
            ->withPivot(['position','notes','added_by'])
            ->withTimestamps()
            ->orderBy('edition_submission.position');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = ['name','slug','icon_path','sort_order','is_active'];

    protected $casts = [
        'is_active' => 'bool',
        'sort_order'=> 'int',
    ];

    // gera slug automaticamente se não vier
    protected static function booted(): void
    {
        static::saving(function (Category $c) {
            if (empty($c->slug)) {
                $c->slug = Str::slug($c->name);
            }
        });
    }

    // URL pública do ícone
    public function getIconUrlAttribute(): ?string
    {
        return $this->icon_path ? Storage::disk('public')->url($this->icon_path) : null;
    }

    public function reviewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reviewer_categories', 'category_id', 'reviewer_id')->withTimestamps();
    }
}

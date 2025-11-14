<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;


    protected $fillable = ['name','email','password','cpf','created_by_id'];

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute(): string
    {
        $path = public_path("images/avatars/{$this->id}.png");
        if (is_file($path)) {
            $url = asset("images/avatars/{$this->id}.png");

            return $url.'?v='.substr(md5_file($path), 0, 8);
        }
        return asset('images/avatar.png');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'reviewer_categories', 'reviewer_id', 'category_id')->withTimestamps();
    }

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function expertiseCategories()
    {
        return $this->belongsToMany(Category::class, 'reviewer_categories', 'reviewer_id', 'category_id')->withTimestamps();
    }

    public function expertise()
    {
        return $this->belongsToMany(Category::class, 'reviewer_categories', 'reviewer_id', 'category_id')->withTimestamps();
    }

    public function openReviews()
    {
        return $this->reviews()->whereIn('status', [Review::ST_ASSIGNED, Review::ST_IN_REVIEW, Review::ST_REV_REQ]);
    }

    public function reviewAssignments()
    {
        return $this->hasMany(ReviewAssignment::class, 'reviewer_id');
    }



    public function submissions()
    {
        return $this->hasMany(Submission::class, 'user_id');
    }
}

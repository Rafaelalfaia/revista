<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;



    protected $fillable = ['name','email','password','cpf','created_by_id'];

    public function createdBy()  { return $this->belongsTo(User::class, 'created_by_id'); }
    public function categories() { return $this->belongsToMany(Category::class)->withTimestamps(); }


    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'reviewer_id');
    }

    public function expertiseCategories()
    {
        return $this->belongsToMany(
            \App\Models\Category::class,
            'reviewer_categories', 'reviewer_id', 'category_id'
        )->withTimestamps();
    }

    public function openReviews()
    {
        return $this->reviews()->whereIn('status', ['atribuida','em_revisao','revisao_solicitada']);
    }


    public function reviewAssignments() {
        return $this->hasMany(\App\Models\ReviewAssignment::class, 'reviewer_id');
    }

}

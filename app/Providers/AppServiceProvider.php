<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Observers\UserObserver;

use App\Models\SubmissionComment;
use App\Policies\SubmissionCommentPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        User::observe(UserObserver::class);

        Gate::policy(SubmissionComment::class, SubmissionCommentPolicy::class);
    }
}

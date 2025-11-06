<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Submission;
use App\Models\SubmissionComment;

class SubmissionCommentPolicy
{

    public function create(User $u, Submission $submission): bool
    {
        if ($u->hasRole('Admin')) return true;
        if ($u->hasRole('Revisor')) {
            return \App\Models\Review::where('submission_id',$submission->id)
                ->where('reviewer_id',$u->id)->exists();
        }
        return false;
    }


    public function resolveAsAuthor(User $u, SubmissionComment $c, Submission $s): bool
    {
        return $u->id === $s->user_id;
    }


    public function verify(User $u, SubmissionComment $c, Submission $s): bool
    {
        if ($u->hasRole('Admin')) return true;
        if ($u->hasRole('Revisor')) {
            return \App\Models\Review::where('submission_id',$s->id)
                ->where('reviewer_id',$u->id)->exists();
        }
        return false;
    }
}

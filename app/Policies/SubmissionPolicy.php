<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
  public function viewAny(User $u): bool
  {
    return true;
  }

  public function view(User $u, Submission $s): bool
  {
    return $s->user_id === $u->id;
  }

  public function create(User $u): bool
  {
    return $u !== null;
  }

  public function update(User $u, Submission $s): bool
  {
    return $s->user_id === $u->id && ($s->isDraft() || $s->status === Submission::ST_REV_REQ);
  }

  public function delete(User $u, Submission $s): bool
  {
    return $s->user_id === $u->id && $s->isDraft();
  }

  public function submit(User $u, Submission $s): bool
  {
    return $s->user_id === $u->id && $s->canSubmit();
  }

  public function upload(User $u, Submission $s): bool
  {
    return $this->update($u, $s);
  }
}

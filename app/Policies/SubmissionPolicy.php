<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
  public function viewAny(User $u): bool
  {
    // pode listar as próprias submissões
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
    // autor do trabalho, e status permitindo edição
    return $s->user_id === $u->id && $s->canEditContent();
  }

  public function delete(User $u, Submission $s): bool
  {
    return $s->user_id === $u->id && $s->isDraft();
  }

  public function submit(User $u, Submission $s): bool
  {
    return $s->user_id === $u->id && $s->canSubmit();
  }

  // Se quiser validar upload/edição de arquivos pela mesma regra:
  public function upload(User $u, Submission $s): bool
  {
    return $this->update($u, $s);
  }
}

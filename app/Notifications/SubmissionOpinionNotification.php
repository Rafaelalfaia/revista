<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubmissionOpinionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $submissionId,
        public string $submissionTitle,
        public string $kind,
        public ?string $notes = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $title = match ($this->kind) {
            'aprovar' => 'Submissão aprovada',
            'rejeitar' => 'Submissão rejeitada',
            'revisar' => 'Correções solicitadas',
            default => 'Atualização na submissão',
        };

        $base = match ($this->kind) {
            'aprovar' => 'Seu projeto foi aprovado. Parabéns.',
            'rejeitar' => 'Seu projeto foi rejeitado.',
            'revisar' => 'Sua submissão requer alterações.',
            default => 'Sua submissão foi atualizada.',
        };

        $message = trim(($this->notes ?? '') !== '' ? $this->notes : $base);

        return [
            'title' => $title,
            'message' => $message,
            'submission_id' => $this->submissionId,
            'submission_title' => $this->submissionTitle,
            'action' => route('autor.submissions.index'),
        ];
    }
}

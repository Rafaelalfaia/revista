<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Submission;
use App\Models\SubmissionComment;

class SubmissionCommentController extends Controller
{

    public function index(Request $r, Submission $submission)
    {
        // Permitir: Admin, autor da submissão ou revisor ATRIBUÍDO
        $u = $r->user();
        $isAdmin  = $u->hasRole('Admin');
        $isAuthor = (int)$submission->user_id === (int)$u->id;
        $isAssignedReviewer = $submission->reviews()
            ->where('reviewer_id', $u->id)
            ->exists();

        abort_unless($isAdmin || $isAuthor || $isAssignedReviewer, 403);

        // Se o usuário for revisor, buscamos o review vinculado (para habilitar "verificar/fechar")
        $review = null;
        if ($isAssignedReviewer) {
            $review = $submission->reviews()
                ->where('reviewer_id', $u->id)
                ->latest('assigned_at')
                ->first();
        }

        // Carregue o mínimo necessário (seções para o data-section-id etc, se você usar)
        $submission->load([
            'rootSections:id,submission_id,title,position',
            // carregue outras relações que seu layout use
        ]);

        return view('submissions.comments', compact('submission','review'));
    }

    public function store(Request $r, Submission $submission)
    {
        $this->authorize('create', [SubmissionComment::class, $submission]);

        $data = $r->validate([
            'section_id'     => ['nullable','integer','exists:submission_sections,id'],
            'parent_id'      => ['nullable','integer','exists:submission_comments,id'],
            'type'           => ['required','in:suggestion,comment,question'],
            'level'          => ['required','in:must_fix,should_fix,nit'],
            'excerpt'        => ['nullable','string','max:10000'],
            'suggested_text' => ['nullable','string'],
        ]);

        // Colunas reais da tabela
        $cols   = Schema::getColumnListing('submission_comments');
        $colset = array_flip($cols); // para lookup O(1)

        // Campo de autor: user_id (preferível) ou author_id (legado)
        $authorKey = isset($colset['user_id'])
            ? 'user_id'
            : (isset($colset['author_id']) ? 'author_id' : null);

        // Valores base
        $excerptValue = trim((string)($data['excerpt'] ?? ''));
        $noteValue    = trim((string)($data['suggested_text'] ?? ''));

        // Se não houve seleção de trecho, derive do texto do comentário
        if ($excerptValue === '') {
            $excerptValue = \Illuminate\Support\Str::limit(
                $noteValue !== '' ? $noteValue : 'Trecho não informado', 300, '…'
            );
        }
        // Se não houve texto do comentário, garanta algo em 'note' (para NOT NULL)
        if ($noteValue === '') {
            $noteValue = $excerptValue ?: 'Sem observações';
        }

        // Monta payload “super-set”
        $payload = [
            'submission_id'  => $submission->id,
            'section_id'     => $data['section_id']     ?? null,
            'parent_id'      => $data['parent_id']      ?? null,
            'type'           => $data['type']           ?? null, // só será enviado se existir a coluna
            'level'          => $data['level']          ?? null,
            'status'         => 'open',

            // Texto do comentário: preenche ambos se existirem
            'note'           => $noteValue,
            'suggested_text' => $noteValue,

            // Trecho selecionado: preenche ambos se existirem
            'quote'          => $excerptValue,
            'excerpt'        => $excerptValue,
        ];

        if ($authorKey) {
            $payload[$authorKey] = $r->user()->id;
        }

        // 🔒 Filtra para enviar SOMENTE colunas que existem nesta base
        $payload = array_intersect_key($payload, $colset);

        SubmissionComment::create($payload);

        if (method_exists($submission, 'recomputeStatus')) {
            $submission->recomputeStatus();
        }

        return back()->with('ok','Comentário/sugestão registrado.');
    }


    public function destroy(Request $r, Submission $submission, SubmissionComment $comment)
    {
        $u = $r->user();

        $isAdmin  = $u->hasRole('Admin');
        $isRev    = $submission->reviews()->where('reviewer_id', $u->id)->exists();

        $isAuthorComment = false;
        if (\Schema::hasColumn('submission_comments','user_id')) {
            $isAuthorComment = (int)($comment->user_id ?? 0) === (int)$u->id;
        } elseif (\Schema::hasColumn('submission_comments','author_id')) {
            $isAuthorComment = (int)($comment->author_id ?? 0) === (int)$u->id;
        }

        abort_unless($isAdmin || $isRev || $isAuthorComment, 403);

        $comment->delete();

        return back()->with('ok','Comentário removido.');
    }


    public function authorResolved(Request $r, Submission $submission, SubmissionComment $comment)
    {
        $this->authorize('resolveAsAuthor', [$comment, $submission]);

        if ((int)$comment->submission_id !== (int)$submission->id) abort(404);
        if ($comment->status !== 'open') return back()->with('error','Este comentário não está aberto.');

        if (Schema::hasColumn('submission_comments', 'resolved_by_author_at')) {
            $comment->update(['resolved_by_author_at' => now()]);
        }

        return back()->with('ok','Marcado como resolvido pelo autor (aguardando verificação do revisor).');
    }

    public function verify(Request $r, Submission $submission, SubmissionComment $comment)
    {
        $this->authorize('verify', [$comment, $submission]);

        if ((int)$comment->submission_id !== (int)$submission->id) abort(404);

        $action = $r->validate(['action'=>['required','in:accept,reopen']])['action'];

        if ($action === 'accept') {
            $updates = ['status' => 'applied'];
            if (Schema::hasColumn('submission_comments','verified_by_reviewer_at')) $updates['verified_by_reviewer_at'] = now();
            if (Schema::hasColumn('submission_comments','resolved_at'))            $updates['resolved_at'] = now();
            if (Schema::hasColumn('submission_comments','resolver_id'))            $updates['resolver_id'] = $r->user()->id;
            $comment->update($updates);
        } else {
            $updates = ['status' => 'open'];
            if (Schema::hasColumn('submission_comments','verified_by_reviewer_at')) $updates['verified_by_reviewer_at'] = null;
            if (Schema::hasColumn('submission_comments','resolved_at'))            $updates['resolved_at'] = null;
            if (Schema::hasColumn('submission_comments','resolved_by_author_at'))  $updates['resolved_by_author_at'] = null;
            $comment->update($updates);
        }

        if (method_exists($submission, 'recomputeStatus')) {
            $submission->recomputeStatus();
        }

        return back()->with('ok', $action === 'accept' ? 'Verificado e fechado.' : 'Reaberto.');
    }
}

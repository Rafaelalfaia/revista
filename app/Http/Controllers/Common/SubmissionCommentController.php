<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Submission;
use App\Models\SubmissionComment;
use App\Models\SubmissionSection;
use Illuminate\Support\Facades\DB;

class SubmissionCommentController extends Controller
{
   public function index(Request $r, \App\Models\Submission $submission)
    {
        $u = $r->user();

        $q = $submission->comments()
            ->with(['user:id,name','section:id,submission_id,title'])
            ->orderByRaw("CASE status WHEN 'open' THEN 0 WHEN 'applied' THEN 1 WHEN 'accepted' THEN 2 ELSE 9 END")
            ->orderByDesc('created_at');

        if ($u->id === $submission->user_id && \Illuminate\Support\Facades\Schema::hasColumn('submission_comments','audience')) {
            $q->whereIn('audience', ['author','both']);
        }

        $comments = $q->paginate(20)->withQueryString();

        $reviews = $submission->reviews()
            ->with('reviewer:id,name')
            ->where('status','parecer_enviado')
            ->latest('submitted_opinion_at')
            ->get();

        $layout = $u->hasRole('Autor') ? 'console.layout-author' : 'console.layout';

        return view('submissions.comments', compact('submission','comments','reviews','layout'));
    }
   public function store(Request $r, Submission $submission)
    {
        $this->authorize('create', [SubmissionComment::class, $submission]);

        $data = $r->validate([
            'section_id'     => ['nullable','integer','exists:submission_sections,id'],
            'parent_id'      => ['nullable','integer','exists:submission_comments,id'],
            'level'          => ['required','in:must_fix,should_fix,nit'],
            'excerpt'        => ['nullable','string','max:10000'],
            'suggested_text' => ['nullable','string','max:10000'],
        ]);

        $sectionId = $data['section_id'] ?? null;
        if ($sectionId && !$submission->sections()->whereKey($sectionId)->exists()) {
            return back()->withErrors(['section_id' => 'Seção inválida para esta submissão.'])->withInput();
        }

        $parentId = $data['parent_id'] ?? null;
        if ($parentId && !SubmissionComment::where('id',$parentId)->where('submission_id',$submission->id)->exists()) {
            return back()->withErrors(['parent_id' => 'Comentário pai inválido para esta submissão.'])->withInput();
        }

        $excerpt = trim((string)($data['excerpt'] ?? ''));
        $note    = trim((string)($data['suggested_text'] ?? ''));

        if ($excerpt === '' && $note !== '') $excerpt = \Illuminate\Support\Str::limit($note, 300, '…');
        if ($note === '' && $excerpt !== '') $note = $excerpt;
        if ($note === '' && $excerpt === '') $note = 'Sem observações';

        $c = new SubmissionComment([
            'submission_id'  => $submission->id,
            'section_id'     => $sectionId,
            'parent_id'      => $parentId,
            'level'          => $data['level'],
            'status'         => 'open',
            'excerpt'        => $excerpt ?: null,
            'suggested_text' => $note ?: null,
            'body'           => $note ?: null,
        ]);

        if (\Illuminate\Support\Facades\Schema::hasColumn('submission_comments','user_id'))   $c->user_id   = $r->user()->id;
        if (\Illuminate\Support\Facades\Schema::hasColumn('submission_comments','author_id')) $c->author_id = $submission->user_id;
        if (\Illuminate\Support\Facades\Schema::hasColumn('submission_comments','audience'))  $c->audience  = 'author';

        $c->save();

        if (method_exists($submission, 'recomputeStatus')) {
            $submission->recomputeStatus();
        }

        return back()->with('ok','Correção/comentário adicionado.');
    }



    public function applySuggestion(Request $r, Submission $submission, SubmissionComment $comment)
    {
        $u = $r->user();
        abort_unless($u->id === $submission->user_id || $u->hasRole('Admin'), 403);

        if (!$comment->section_id || !$comment->suggested_text) {
            return back()->withErrors(['comment' => 'Este comentário não possui sugestão aplicável.']);
        }

        $section = $submission->sections()->whereKey($comment->section_id)->firstOrFail();
        $old     = (string)($section->content ?? '');
        $excerpt = trim((string)($comment->excerpt ?? ''));
        $suggest = (string)$comment->suggested_text;

        $replaced = null;
        if ($excerpt !== '') {
            $norm   = preg_replace('/\s+/u',' ', $excerpt);
            $parts  = preg_split('/\s+/u', $norm, -1, PREG_SPLIT_NO_EMPTY);
            $regex  = '/'.implode('(?:\s|<[^>]+>)*', array_map(fn($w)=>preg_quote($w,'/'), $parts)).'/iu';
            $replaced = preg_replace($regex, $suggest, $old, 1);
        }

        if ($replaced !== null && $replaced !== $old) {
            $section->content = $replaced;
        } else {
            $section->content = trim($old."\n\n".$suggest);
        }

        $historyId = null;
        if (Schema::hasTable('submission_section_histories')) {
            $historyId = DB::table('submission_section_histories')->insertGetId([
                'submission_id' => $submission->id,
                'section_id'    => $section->id,
                'edited_by'     => $u->id,
                'old_content'   => $old,
                'new_content'   => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $section->save();

        if ($historyId) {
            DB::table('submission_section_histories')->where('id', $historyId)->update([
                'new_content' => $section->content,
                'updated_at'  => now(),
            ]);
        }

        $updates = ['status' => 'applied'];
        if (Schema::hasColumn('submission_comments','resolved_by_author_at')) $updates['resolved_by_author_at'] = now();
        if (Schema::hasColumn('submission_comments','resolved_at'))           $updates['resolved_at']           = now();
        $comment->update($updates);

        $submission->recomputeStatus();

        return back()->with('ok', 'Sugestão aplicada na seção.');
    }



    public function destroy(Request $r, Submission $submission, SubmissionComment $comment)
    {
        $u = $r->user();

        $isAdmin = $u->hasRole('Admin');
        $isRev = $submission->reviews()->where('reviewer_id', $u->id)->exists();

        $isAuthorComment = false;
        if (Schema::hasColumn('submission_comments','user_id')) {
            $isAuthorComment = (int)($comment->user_id ?? 0) === (int)$u->id;
        } elseif (Schema::hasColumn('submission_comments','author_id')) {
            $isAuthorComment = (int)($comment->author_id ?? 0) === (int)$u->id;
        }

        abort_unless($isAdmin || $isRev || $isAuthorComment, 403);

        $comment->delete();

        return back()->with('ok','Comentário removido.');
    }

    public function authorResolved(Request $r, \App\Models\Submission $submission, \App\Models\SubmissionComment $comment)
    {

        abort_unless($submission->id === $comment->submission_id, 404);
        abort_unless($submission->user_id === $r->user()->id, 403);


        $comment->status = 'applied';
        if (Schema::hasColumn('submission_comments','closed_by')) {
            $comment->closed_by = 'author';
        }
        if (Schema::hasColumn('submission_comments','closed_at')) {
            $comment->closed_at = now();
        }
        $comment->save();

        // Opcional: reprocessar status da submissão se tiver esse método
        if (method_exists($submission, 'recomputeStatus')) {
            $submission->recomputeStatus();
        }

        return back()->with('ok','Comentário marcado como resolvido.');
    }

    public function reopen(Request $r, Submission $submission, SubmissionComment $comment)
    {
        $user = $r->user();
        abort_unless($user->hasAnyRole(['Revisor','Admin','Coordenador']), 403);
        abort_unless($submission->id === $comment->submission_id, 404);

        if ((int)$comment->resolver_id === (int)$submission->user_id) {
            return back()->with('error', 'Correção enviada pelo autor não pode ser reaberta. Crie uma nova correção.');
        }

        if (property_exists($comment, 'status')) {
            $comment->status = 'open';
        }
        $comment->resolver_id = null;
        $comment->resolved_at = null;
        $comment->save();

        return back()->with('ok', 'Comentário reaberto.');
    }



    public function verify(Request $r, Submission $submission, SubmissionComment $comment)
    {
        $this->authorize('verify', [$comment, $submission]);

        if ((int)$comment->submission_id !== (int)$submission->id) abort(404);

        $action = $r->validate(['action'=>['required','in:accept,reopen']])['action'];

        if ($action === 'accept') {
            $updates = ['status' => 'applied'];
            if (Schema::hasColumn('submission_comments','verified_by_reviewer_at')) $updates['verified_by_reviewer_at'] = now();
            if (Schema::hasColumn('submission_comments','resolved_at')) $updates['resolved_at'] = now();
            if (Schema::hasColumn('submission_comments','resolver_id')) $updates['resolver_id'] = $r->user()->id;
            $comment->update($updates);
        } else {
            $updates = ['status' => 'open'];
            if (Schema::hasColumn('submission_comments','verified_by_reviewer_at')) $updates['verified_by_reviewer_at'] = null;
            if (Schema::hasColumn('submission_comments','resolved_at')) $updates['resolved_at'] = null;
            if (Schema::hasColumn('submission_comments','resolved_by_author_at')) $updates['resolved_by_author_at'] = null;
            $comment->update($updates);
        }

        if (method_exists($submission, 'recomputeStatus')) {
            $submission->recomputeStatus();
        }

        return back()->with('ok', $action === 'accept' ? 'Verificado e fechado.' : 'Reaberto.');
    }
}

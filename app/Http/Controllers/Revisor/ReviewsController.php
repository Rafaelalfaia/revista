<?php

namespace App\Http\Controllers\Revisor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:reviews.view_assigned']);
    }

    public function index(Request $r)
    {
        $q = trim((string) $r->query('q', ''));
        $status = $r->query('status');

        $reviews = \App\Models\Review::with([
                'submission' => fn($q2) => $q2
                    ->select('id','title','slug','status','submitted_at','user_id')
                    ->with(['categories:id,name', 'categories' => fn($c)=>$c->orderBy('name')]),
            ])
            ->where('reviewer_id', $r->user()->id)
            ->when($q !== '', fn($qq) =>
                $qq->whereHas('submission', fn($s) =>
                    $s->where('title', 'ILIKE', "%{$q}%")
                )
            )
            ->when($status, fn($qq) => $qq->where('status', $status))
            ->orderByRaw('COALESCE(assigned_at, created_at) DESC')
            ->paginate(12)
            ->withQueryString();

        $submissionIds = $reviews->pluck('submission_id')->all();
        $blockingCounts = \App\Models\SubmissionComment::selectRaw('submission_id, COUNT(*) AS qtd')
            ->whereIn('submission_id', $submissionIds)
            ->where('level', 'must_fix')->where('status', 'open')
            ->groupBy('submission_id')
            ->pluck('qtd','submission_id');

        return view('revisor.reviews.index', [
            'reviews' => $reviews,
            'q' => $q,
            'status' => $status,
            'blockingCounts' => $blockingCounts,
        ]);
    }

    public function show(\App\Models\Review $review)
    {
        abort_unless($review->reviewer_id === auth()->id() || auth()->user()->hasRole('Admin'), 403);

        $review->load([
            'submission' => fn($q) => $q->select('id','title','slug','status','submitted_at','user_id')
                                        ->with([
                                            'categories:id,name',
                                            'rootSections:id,submission_id,title,content,position,parent_id'
                                        ]),
            'submission.reviews.reviewer:id,name',
        ]);

        return view('revisor.reviews.show', compact('review'));
    }

    public function submitOpinion(Request $r, Review $review)
    {
        $this->authorizeSelf($review);

        $data = $r->validate([
            'recommendation' => ['required','in:aprovar,rejeitar,revisar'],
            'notes' => ['nullable','string','max:5000'],
        ]);

        if ($data['recommendation'] === 'aprovar') {
            $hasBlocking = $review->submission->comments()
                ->where('level','must_fix')->where('status','open')->exists();
            if ($hasBlocking) {
                return back()->withErrors(['recommendation' => 'Há pendências bloqueantes (must_fix) abertas.'])->withInput();
            }
        }

        $review->recommendation = $data['recommendation'];
        $review->notes = $data['notes'] ?? null;
        $review->submitted_opinion_at = now();
        $review->status = 'parecer_enviado';

        if ($data['recommendation'] === 'revisar') {
            $review->submission->update(['status' => 'revisao_solicitada']);
        } elseif ($data['recommendation'] === 'aprovar') {
            $review->submission->update(['status' => 'aceito']);
        } elseif ($data['recommendation'] === 'rejeitar') {
            $review->submission->update(['status' => 'rejeitado']);
        }

        $review->save();

        $authorId = $review->submission->user_id;
        $author = \App\Models\User::find($authorId);
        if ($author) {
            $author->notify(new \App\Notifications\SubmissionOpinionNotification(
                submissionId: $review->submission->id,
                submissionTitle: $review->submission->title,
                kind: $data['recommendation'],
                notes: $data['notes'] ?? null
            ));
        }

        return redirect()->route('revisor.reviews.index')->with('ok', 'Parecer enviado.');
    }



    protected function authorizeSelf(Review $review): void
    {
        abort_unless($review->reviewer_id === auth()->id() || auth()->user()->hasRole('Admin'), 403);
    }
}

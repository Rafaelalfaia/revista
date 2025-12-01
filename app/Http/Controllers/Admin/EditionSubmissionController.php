<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Review;


class EditionSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:Admin']);
    }

   public function index(Request $r, Edition $edition)
    {
        $q = trim((string) $r->query('q',''));

        $attachedIds = $edition->submissions()->pluck('submissions.id');

        $eligible = \App\Models\Submission::query()
            ->with(['user:id,name,email'])
            ->when($q !== '', fn($qq) =>
                $qq->where(function($w) use ($q){
                    $w->where('title','ilike',"%{$q}%")
                    ->orWhere('slug','ilike',"%{$q}%");
                })
            )
            ->whereNotIn('id',$attachedIds)
            ->where(function($w){
                $w->where('status','aceito')
                ->orWhereExists(function($sub){
                    $sub->select(\DB::raw(1))
                        ->from('reviews')
                        ->whereColumn('reviews.submission_id','submissions.id')
                        ->whereNotNull('submitted_opinion_at')
                        ->whereIn(\DB::raw('lower(recommendation)'), [
                            'aceitar','accept','aprovado','approve','aprovada'
                        ]);
                });
            })
            ->latest('accepted_at')
            ->limit(50)
            ->get();

        $current = $edition->submissions()->with(['user:id,name'])->withPivot(['position','notes','added_by'])->get();

        $ids = $eligible->pluck('id')->merge($current->pluck('id'))->unique()->values();

        $reviewersBySubmission = \App\Models\Review::query()
            ->select(['id','submission_id','reviewer_id','submitted_opinion_at','recommendation'])
            ->whereIn('submission_id', $ids)
            ->whereNotNull('submitted_opinion_at')
            ->with(['reviewer:id,name'])
            ->get()
            ->groupBy('submission_id')
            ->map(fn($rows) => $rows->pluck('reviewer.name')->filter()->unique()->values())
            ->all();

        return view('admin.editions.submissions', compact('edition','current','eligible','q','reviewersBySubmission'));
    }



    public function store(Request $r, Edition $edition)
{
    $data = $r->validate([
        'submission_id' => ['required','integer','exists:submissions,id'],
        'featured'      => ['nullable','boolean'],
    ]);

    $sid      = (int) $data['submission_id'];
    $featured = !empty($data['featured']); // 1/0 -> bool

    $exists = $edition->submissions()->where('submissions.id',$sid)->exists();
    if ($exists) {
        return back()->with('warn','Já está na edição.');
    }

    $isEligible = Submission::query()
        ->where('id',$sid)
        ->where(function($w){
            $w->where('status','aceito')
             ->orWhereExists(function($sub){
                $sub->select(DB::raw(1))
                    ->from('reviews')
                    ->whereColumn('reviews.submission_id','submissions.id')
                    ->whereNotNull('submitted_opinion_at')
                    ->whereIn(DB::raw('lower(recommendation)'), [
                        'aceitar','accept','aprovado','approve','aprovada'
                    ]);
             });
        })
        ->exists();

    if (!$isEligible) {
        return back()->with('error','Esta submissão ainda não está aprovada para publicação.');
    }

    $nextPos = (int) ($edition->submissions()->max('edition_submission.position') ?? 0) + 1;

    $edition->submissions()->attach($sid, [
        'position' => $nextPos,
        'added_by' => $r->user()->id,
        'notes'    => $featured ? 'featured' : null,
    ]);

    return back()->with('ok','Submissão adicionada à edição.');
}


    public function destroy(Request $r, Edition $edition, Submission $submission)
    {
        $edition->submissions()->detach($submission->id);
        return back()->with('ok','Submissão removida da edição.');
    }

    public function toggleHighlight(Request $r, Edition $edition, Submission $submission)
    {
        $rel = $edition->submissions()->where('submissions.id', $submission->id)->first();
        if (!$rel) {
            return back()->with('error','Publicação não está vinculada a esta edição.');
        }

        $pivot   = $rel->pivot;
        $current = trim((string)($pivot->notes ?? ''));
        $new     = $current === 'featured' ? null : 'featured';

        $edition->submissions()->updateExistingPivot($submission->id, [
            'notes' => $new,
        ]);

        return back()->with('ok', $new === 'featured' ? 'Publicação destacada.' : 'Destaque removido.');
    }


    public function reorder(Request $r, Edition $edition)
    {
        $data = $r->validate([
            'order' => ['required','array','min:1'],
            'order.*' => ['integer','distinct']
        ]);

        $ids = $edition->submissions()->pluck('submissions.id')->all();
        $incoming = $data['order'];

        sort($ids);
        $cmp = $incoming; sort($cmp);

        if ($ids !== $cmp) {
            return back()->with('error','Lista inválida para reordenar.');
        }

        DB::transaction(function() use ($edition, $incoming){
            foreach ($incoming as $pos => $sid) {
                $edition->submissions()->updateExistingPivot($sid, ['position' => $pos + 1]);
            }
        });

        return back()->with('ok','Ordem atualizada.');
    }
}

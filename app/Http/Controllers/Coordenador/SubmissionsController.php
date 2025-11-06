<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use App\Actions\AssignRandomCorrection;


class SubmissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:submissions.view']);
    }

    public function index(Request $r)
    {
        $coordId = $r->user()->id;

        $myReviewerIds = User::role('Revisor')
            ->where('created_by_id', $coordId)
            ->pluck('id');

        $q      = trim($r->get('q',''));
        $status = $r->get('status');

        $subs = Submission::query()
            ->whereHas('reviews', fn($w) => $w->whereIn('reviewer_id', $myReviewerIds))
            ->when($q, fn($w) => $w->where('title','ilike',"%{$q}%"))
            ->when($status, fn($w) => $w->where('status',$status))
            ->with(['reviews.reviewer:id,name,created_by_id'])
            ->orderByDesc('updated_at')
            ->paginate(15)->withQueryString();

        return view('coordenador.submissions.index', compact('subs','q','status'));
    }

    public function show(Request $r, Submission $submission)
    {
        $coordId = $r->user()->id;

        $hasMine = $submission->reviews()
            ->whereHas('reviewer', fn($w) => $w->where('created_by_id', $coordId))
            ->exists();

        if (!$hasMine) {
            return redirect()->route('coordenador.submissions.index')->with('err','Esta submissão não pertence aos seus revisores.');
        }

        $submission->load(['reviews.reviewer:id,name', /* Se mexer tome cuidado Alunos */]);

        return view('coordenador.submissions.show', compact('submission'));
    }

    public function distribuirCorrecao(Request $r, \App\Models\Submission $submission)
    {
        $this->middleware('permission:submissions.assign');

        $coordId = $r->user()->id;
        $catId   = $r->integer('category_id') ?: null;

        $ok = AssignRandomCorrection::assign($submission, $coordId, $catId);

        return back()->with($ok ? 'ok' : 'err', $ok ? 'Correção distribuída.' : 'Nenhum revisor elegível para esta categoria.');
    }
}

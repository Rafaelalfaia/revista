<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Category;
use App\Models\User;
use App\Models\SubmissionComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $status     = $request->get('status');              // string|null
        $q          = $request->get('q');                   // string|null
        $from       = $request->date('from');               // Carbon|null
        $to         = $request->date('to');                 // Carbon|null
        $authorId   = $request->integer('author_id');       // int|null
        $categoryId = $request->integer('category_id');     // int|null
        $dateField  = in_array($request->get('date_field'), ['created_at','submitted_at'], true)
                        ? $request->get('date_field') : 'created_at';

        $rows = Submission::with('author')
            ->when($authorId, fn ($w) => $w->where('user_id', $authorId))
            ->when($categoryId, fn ($w) =>
                $w->whereHas('categories', fn ($c) => $c->where('categories.id', $categoryId)))
            ->when($from, fn ($w) => $w->whereDate($dateField, '>=', $from))
            ->when($to,   fn ($w) => $w->whereDate($dateField, '<=', $to))
            ->when($status, fn ($w) => $w->status($status))      // aplica scope só se vier
            ->when($q,      fn ($w) => $w->search($q))           // idem
            ->orderByRaw("CASE status
                WHEN 'submetido' THEN 0
                WHEN 'em_triagem' THEN 1
                WHEN 'em_revisao' THEN 2
                WHEN 'revisao_solicitada' THEN 3
                WHEN 'aceito' THEN 4
                WHEN 'rejeitado' THEN 5
                ELSE 6 END, {$dateField} DESC")
            ->paginate(15)->withQueryString();

        $stats = Submission::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total','status');

        $categories = Category::orderBy('name')->get(['id','name']);
        $authors    = User::whereIn('id', Submission::select('user_id')->distinct())
                          ->orderBy('name')->get(['id','name']);

        return view('admin.submissions.index', compact(
            'rows','q','status','from','to','stats','categories','authors','authorId','categoryId','dateField'
        ));
    }

    public function show(Submission $submission)
    {
        $submission->load(['author','files','categories']);
        return view('admin.submissions.show', compact('submission'));
    }

    public function transition(Request $request, Submission $submission)
    {
        $data = $request->validate([
            'action'  => ['required','in:desk_reject,request_fixes,send_to_review,accept,reject'],
            'message' => ['nullable','string','max:2000'],
        ]);

        $a   = $data['action'];
        $msg = $data['message'] ?? null;

        switch ($a) {
            case 'desk_reject':
                $submission->status = Submission::ST_REJECTED;
                $submission->triaged_at = now();
                $submission->save();
                return back()->with('ok','Desk reject aplicado.');

            case 'request_fixes':
                $submission->status = Submission::ST_REV_REQ;
                $submission->save();
                // $msg opcional: enviar notificação/email depois
                return back()->with('ok','Correções solicitadas ao autor.');

            case 'send_to_review':
                $submission->status = Submission::ST_SCREEN;
                $submission->triaged_at = now();
                $submission->save();
                return back()->with('ok','Submissão pronta para atribuições de revisão.');

            case 'accept':
                $submission->status = Submission::ST_ACCEPTED;
                $submission->accepted_at = now();
                $submission->save();
                return back()->with('ok','Submissão aceita.');

            case 'reject':
                $submission->status = Submission::ST_REJECTED;
                $submission->save();
                return back()->with('ok','Submissão rejeitada.');
        }

        abort(400, 'Ação inválida.');
    }

    /** Modo de leitura (1 ou 2 “páginas” + comentários) */
    public function read(Submission $submission)
    {
        $sections = $submission->sections()
            ->roots()
            ->orderBy('position')
            ->get();

        return view('admin.submissions.read', compact('submission','sections'));
    }

    /** Lista comentários (JSON) */
    public function commentsIndex(Submission $submission): JsonResponse
    {
        $rows = SubmissionComment::where('submission_id', $submission->id)
            ->latest()
            ->get(['id','submission_id','section_id','quote','note','page_mode','created_at']);

        return response()->json($rows);
    }

    /** Cria comentário (JSON) */
    public function commentsStore(Request $request, Submission $submission): JsonResponse
    {
        $data = $request->validate([
            'section_id' => ['required','integer','exists:submission_sections,id'],
            'quote'      => ['required','string','min:2'],
            'note'       => ['required','string','min:2'],
            'page_mode'  => ['nullable','in:single,dual'],
        ]);

        $row = SubmissionComment::create([
            'submission_id' => $submission->id,
            'section_id'    => $data['section_id'],
            'user_id'       => $request->user()->id,
            'quote'         => $data['quote'],
            'note'          => $data['note'],
            'page_mode'     => $data['page_mode'] ?? null,
        ]);

        return response()->json([
            'id'          => $row->id,
            'section_id'  => $row->section_id,
            'quote'       => $row->quote,
            'note'        => $row->note,
            'page_mode'   => $row->page_mode,
            'created_at'  => $row->created_at,
        ]);
    }

    public function commentsDestroy(Request $request, Submission $submission, SubmissionComment $comment): JsonResponse
    {
        abort_if($comment->submission_id !== $submission->id, 404);

        if (!$request->user()->hasRole('Admin') && $request->user()->id !== $comment->user_id) {
            abort(403);
        }

        $id = $comment->id;
        $comment->delete();

        return response()->json(['ok' => true, 'id' => $id]);
    }

}

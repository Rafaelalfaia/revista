<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\SubmissionComment;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use App\Models\User;


class SubmissionController extends Controller
{
    // Lista minhas submissões
    public function index(Request $request)
    {
        $status     = $request->get('status');
        $q          = $request->get('q');
        $from       = $request->date('from');
        $to         = $request->date('to');
        $authorId   = $request->integer('author_id');
        $categoryId = $request->integer('category_id');
        $dateField  = in_array($request->get('date_field'), ['created_at','submitted_at'], true)
                        ? $request->get('date_field') : 'created_at';

        $rows = Submission::with('author')
            ->when($authorId, fn ($w) => $w->where('user_id', $authorId))
            ->when($categoryId, fn ($w) =>
                $w->whereHas('categories', fn ($c) => $c->where('categories.id', $categoryId)))
            ->when($from, fn ($w) => $w->whereDate($dateField, '>=', $from))
            ->when($to,   fn ($w) => $w->whereDate($dateField, '<=', $to))
            ->status($status)
            ->search($q)
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

        // dados para selects
        $categories = Category::orderBy('name')->get(['id','name']);
        $authors    = User::whereIn('id', Submission::select('user_id')->distinct())
                        ->orderBy('name')->get(['id','name']);

        return view('admin.submissions.index', compact(
            'rows','q','status','from','to','stats','categories','authors','authorId','categoryId','dateField'
        ));
    }

    // Tela de novo rascunho
    public function create()
    {
        $tipos = [
            Submission::TIPO_ARTIGO,
            Submission::TIPO_MONOGRAFIA,
            Submission::TIPO_DISSERTACAO,
            Submission::TIPO_TESE,
            Submission::TIPO_RELATO,
            Submission::TIPO_RESENHA,
        ];
        return view('autor.submissions.create', compact('tipos'));
    }

    public function read(Submission $submission)
    {
        // carrega as seções raiz na ordem
        $sections = $submission->sections()
            ->roots()
            ->orderBy('position')
            ->get();

        return view('admin.submissions.read', compact('submission','sections'));
    }

    public function commentsIndex(Submission $submission): JsonResponse
    {
        $rows = SubmissionComment::where('submission_id', $submission->id)
            ->latest()
            ->get(['id','submission_id','section_id','quote','note','page_mode','created_at']);

        return response()->json($rows);
    }

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





    // Cria rascunho
    public function store(Request $r)
    {
        $data = $r->validate([
            'title'         => ['required','string','max:255'],
            'tipo_trabalho' => ['required', Rule::in([
                Submission::TIPO_ARTIGO,
                Submission::TIPO_MONOGRAFIA,
                Submission::TIPO_DISSERTACAO,
                Submission::TIPO_TESE,
                Submission::TIPO_RELATO,
                Submission::TIPO_RESENHA,
            ])],
            'abstract'      => ['nullable','string'],
            'language'      => ['nullable','string','max:10'],
            'keywords'      => ['nullable','array'],
        ]);

        $sub = Submission::create([
            'user_id'       => $r->user()->id,
            'title'         => $data['title'],
            'slug'          => Str::slug($data['title']),
            'tipo_trabalho' => $data['tipo_trabalho'],
            'abstract'      => $data['abstract'] ?? null,
            'language'      => $data['language'] ?? 'pt-BR',
            'keywords'      => $data['keywords'] ?? [],
            'meta'          => [],
            'status'        => Submission::ST_RASCUNHO,
        ]);

        return redirect()->route('autor.submissions.edit', $sub)
            ->with('ok', 'Rascunho criado. Você pode começar a montar as seções.');
    }

    // Editar rascunho (metadados básicos; as seções ficam em outro controller)
    public function edit(Submission $submission)
    {
        $this->authorize('update', $submission); // Policy recomendada
        return view('autor.submissions.edit', compact('submission'));
    }

    // Atualiza metadados (título, resumo, palavras-chave, idioma)
    public function update(Request $r, Submission $submission)
    {
        $this->authorize('update', $submission);

        $data = $r->validate([
            'title'    => ['required','string','max:255'],
            'abstract' => ['nullable','string'],
            'language' => ['nullable','string','max:10'],
            'keywords' => ['nullable','array'],
        ]);

        $submission->update([
            'title'    => $data['title'],
            'abstract' => $data['abstract'] ?? null,
            'language' => $data['language'] ?? $submission->language,
            'keywords' => $data['keywords'] ?? $submission->keywords,
            // slug: mantém; se quiser atualizar: 'slug' => Str::slug($data['title']),
        ]);

        return back()->with('ok', 'Metadados atualizados.');
    }

    // Enviar para triagem (muda status, registra submitted_at)
    public function submit(Submission $submission)
    {
        $this->authorize('update', $submission);
        abort_unless($submission->canSubmit(), 422, 'Esta submissão não está em rascunho.');

        // (Opcional) validar presença de seções obrigatórias, referências etc. aqui
        $submission->submit();

        return redirect()->route('autor.submissions.index')->with('ok','Submissão enviada para triagem.');
    }

    // Remover rascunho (hard delete). Pode trocar por softDeletes se preferir.
    public function destroy(Submission $submission)
    {
        $this->authorize('delete', $submission);
        abort_unless($submission->status === Submission::ST_RASCUNHO, 422, 'Só é possível remover rascunhos.');

        $submission->delete();
        return redirect()->route('autor.submissions.index')->with('ok','Rascunho removido.');
    }
}

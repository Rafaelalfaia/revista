<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionSection;
use App\Models\SubmissionAsset;
use App\Models\SubmissionFile;
use App\Models\SubmissionReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Services\SubmissionStructureService;
use App\Models\User;
use App\Services\AutoAssignService;
use Illuminate\Support\Facades\Schema;


class SubmissionController extends Controller
{
    public function index(Request $r)
    {
        $viewer = $r->user();
        $authorId = (int) ($r->integer('author_id') ?: $viewer->id);
        if ($authorId !== $viewer->id) {
            abort_unless(($viewer->hasRole('Admin') || $viewer->can('submissions.viewAny')), 403);
        }

        $categoryId = $r->integer('category_id');
        $from = $r->date('from');
        $to = $r->date('to');
        $dateField = in_array($r->get('date_field'), ['created_at','submitted_at'], true) ? $r->get('date_field') : 'created_at';

        $subs = Submission::query()
            ->where('user_id', $authorId)
            ->when($categoryId, fn ($q) => $q->whereHas('categories', fn ($c) => $c->where('categories.id', $categoryId)))
            ->when($from, fn ($q) => $q->whereDate($dateField, '>=', $from))
            ->when($to, fn ($q) => $q->whereDate($dateField, '<=', $to))
            ->smartOrder()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id','name']);
        $authors = User::whereIn('id', Submission::select('user_id')->distinct())->orderBy('name')->get(['id','name']);

        return view('autor.submissions.index', compact('subs','categories','authors','authorId','categoryId','from','to','dateField'));
    }

    public function create()
    {
        return view('autor.submissions.create');
    }

    public function store(Request $r, SubmissionStructureService $svc)
    {
        $data = $r->validate([
            'title' => ['required','string','max:255'],
            'abstract' => ['nullable','string'],
            'language' => ['nullable','string','max:10'],
            'keywords' => ['nullable','string'],
            'tipo_trabalho' => ['required','string','max:50'],
            'categories' => ['nullable','array'],
            'categories.*' => ['integer','exists:categories,id'],
            'primary_category_id'=> ['nullable','integer','exists:categories,id'],
        ]);

        $submission = Submission::create([
            'user_id' => $r->user()->id,
            'title' => $data['title'],
            'slug' => Str::slug($data['title']).'-'.Str::random(5),
            'abstract' => $data['abstract'] ?? null,
            'language' => $data['language'] ?? 'pt-BR',
            'keywords' => isset($data['keywords'])
                ? collect(explode(',', $data['keywords']))->map(fn($s)=>trim($s))->filter()->values()->all()
                : [],
            'tipo_trabalho' => $data['tipo_trabalho'],
            'status' => Submission::ST_DRAFT,
        ]);

        $catIds = array_map('intval', $r->input('categories', []));
        $primary = $r->integer('primary_category_id');
        if (method_exists($submission, 'syncCategories')) {
            if ($primary && !in_array($primary, $catIds, true)) {
                $primary = $catIds[0] ?? null;
            }
            $submission->syncCategories($catIds, $primary);
        }

        $svc->bootstrap($submission);

        return redirect()->route('autor.submissions.wizard', $submission)->with('ok', 'Submissão criada. Preencha as seções do wizard.');
    }

    public function wizard(Submission $submission)
    {
        $sections = $submission->sections()->roots()->with(['assets','children.assets','children.children.assets'])->get();
        $first = $sections->first();
        $allCats = Category::orderBy('sort_order')->orderBy('name')->get(['id','name','slug']);
        $selectedCatIds = $submission->categories()->pluck('categories.id')->all();
        $primaryCategoryId = $submission->categories()->wherePivot('is_primary', true)->value('categories.id');

        return view('autor.submissions.wizard.index', [
            'sub' => $submission,
            'sections' => $sections,
            'first' => $first,
            'allCats' => $allCats,
            'selectedCatIds' => $selectedCatIds,
            'primaryCategoryId' => $primaryCategoryId,
        ]);
    }

    public function editSection(Request $r, Submission $submission, SubmissionSection $section)
    {
        $this->ownsOrAbort($r, $submission);
        $this->belongsToOrAbort($section, $submission);

        [$prevId, $nextId] = $this->neighborSectionIds($submission, $section);

        $assets = $submission->assets()->where('section_id', $section->id)->orderBy('order')->get();

        return view('autor.submissions.wizard.section', [
            'sub' => $submission,
            'sec' => $section,
            'prevId' => $prevId,
            'nextId' => $nextId,
            'assets' => $assets,
        ]);
    }

    public function updateSection(Request $r, Submission $submission, SubmissionSection $section)
    {
        $this->ownsOrAbort($r, $submission);
        $this->belongsToOrAbort($section, $submission);

        $data = $r->validate([
            'title' => ['required','string','max:255'],
            'content' => ['nullable','string'],
            'show_number' => ['nullable','boolean'],
            'show_in_toc' => ['nullable','boolean'],
            'numbering' => ['nullable','string','max:20'],
        ]);

        $section->title = $data['title'];
        $section->content = $data['content'] ?? null;
        $section->show_number = (bool)($data['show_number'] ?? false);
        $section->show_in_toc = (bool)($data['show_in_toc'] ?? true);
        $section->numbering = $data['numbering'] ?? null;
        $section->save();

        $nav = $r->input('nav');
        [$prevId, $nextId] = $this->neighborSectionIds($submission, $section);

        if ($nav === 'prev' && $prevId) {
            return redirect()->route('autor.submissions.section.edit', [$submission, $prevId])->with('ok', 'Seção salva. Voltando para a anterior.');
        }
        if ($nav === 'next' && $nextId) {
            return redirect()->route('autor.submissions.section.edit', [$submission, $nextId])->with('ok', 'Seção salva. Indo para a próxima.');
        }

        if (Schema::hasTable('submission_section_histories')) {
            DB::table('submission_section_histories')->insert([
                'submission_id' => $submission->id,
                'section_id'    => $section->id,
                'edited_by'     => $r->user()->id,
                'old_content'   => $section->content,
                'new_content'   => $data['content'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        return back()->with('ok', 'Seção salva.');
    }

    public function updateAfterSubmit(Request $r, Submission $submission)
{
    $this->ownsOrAbort($r, $submission);

    if (is_null($submission->submitted_at)) {
        return back()->with('error', 'Envie para análise pela primeira vez antes de atualizar.');
    }

    $hasOpen = $submission->comments()->where('status','open')->exists();
    if ($hasOpen) {
        return back()->with('error', 'Há correções pendentes. Resolva-as antes de atualizar o projeto.');
    }

    $errors = [];
    if (!$submission->title) $errors[] = 'Título é obrigatório.';
    if (mb_strlen((string) $submission->abstract) < 50) $errors[] = 'Resumo muito curto (mín. 50 caracteres).';

    $total = $submission->sections()->count();
    $filled = $submission->sections()
        ->whereNotNull('content')
        ->whereRaw("length(trim(content)) >= 30")
        ->count();
    if ($total === 0 || $filled < $total) $errors[] = 'Todas as seções devem estar preenchidas.';

    $hasPrimaryCategory = $submission->categories()->wherePivot('is_primary', true)->exists();
    if (!$hasPrimaryCategory) $errors[] = 'Selecione uma categoria (obrigatória).';

    if (!empty($errors)) {
        return back()->withErrors($errors)->with('error', 'Revise os itens antes de atualizar.');
    }

    if ($submission->isFillable('resubmitted_at')) {
        $submission->resubmitted_at = now();
    }
    if ($submission->isFillable('author_pinged_review_at')) {
        $submission->author_pinged_review_at = now();
    }

    $submission->save();

    return back()->with('ok','Projeto atualizado para a revisão.');
}


    public function bootstrapSections(Request $r, Submission $submission)
    {
        $this->ownsOrAbort($r, $submission);

        if ($submission->sections()->count() > 0) {
            return back()->with('warn', 'A submissão já possui seções. Nada foi alterado.');
        }

        $this->bootstrapSectionsInternal($submission);

        return back()->with('ok', 'Seções padrão criadas.');
    }

    public function updateMetadata(Request $r, Submission $submission, SubmissionStructureService $svc)
    {
        $oldType = $submission->tipo_trabalho;
        $validTypes = array_keys(Submission::TYPE_LABELS ?? []);
        $isCategoriesContext = $r->string('context')->toString() === 'categories';

        $rules = [
            'title' => ['nullable','string','max:255'],
            'abstract' => ['nullable','string'],
            'language' => ['nullable','string','max:10'],
            'keywords' => ['nullable','string'],
            'tipo_trabalho' => ['nullable','string', Rule::in($validTypes)],
        ];

        if ($isCategoriesContext) {
            $rules['category_id'] = ['required','integer','exists:categories,id'];
        }

        $data = $r->validate($rules);

        if (!$isCategoriesContext) {
            $keywords = $submission->keywords;
            if (array_key_exists('keywords', $data) && $data['keywords'] !== null) {
                $keywords = collect(explode(',', (string)$data['keywords']))->map(fn($s) => trim($s))->filter()->values()->unique()->all();
            }

            $submission->fill([
                'title' => array_key_exists('title', $data) ? $data['title'] : $submission->title,
                'abstract' => array_key_exists('abstract', $data) ? $data['abstract'] : $submission->abstract,
                'language' => array_key_exists('language', $data) ? $data['language'] : $submission->language,
                'tipo_trabalho' => array_key_exists('tipo_trabalho', $data) ? $data['tipo_trabalho'] : $submission->tipo_trabalho,
                'keywords' => $keywords,
            ])->save();

            $newType = $submission->tipo_trabalho;
            if ($oldType !== $newType) {
                $nonEmpty = method_exists($svc, 'countNonEmpty')
                    ? $svc->countNonEmpty($submission)
                    : $submission->sections()->withCount('assets')->get()->reduce(function ($c, $sec) {
                        $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string)$sec->content)));
                        return $c + (($plain !== '' || $sec->assets_count > 0) ? 1 : 0);
                    }, 0);

                $mode = $nonEmpty > 0 ? 'create_missing' : 'hard_reset';
                $svc->syncToType($submission, $newType, $mode);

                return back()->with('ok', $mode === 'hard_reset'
                    ? 'Tipo alterado. Seções do novo tipo aplicadas.'
                    : 'Tipo alterado. Seções adicionadas/reordenadas; seu conteúdo foi preservado.');
            }

            return back()->with('ok', 'Metadados atualizados.');
        }

        $categoryId = (int) $r->input('category_id');

        if (method_exists($submission, 'syncCategories')) {
            $submission->syncCategories([$categoryId], $categoryId);
        } else {
            $submission->categories()->sync([$categoryId => ['is_primary' => true]]);
        }

        return back()->with('ok', 'Categoria atualizada.');
    }

    public function sectionsReset(Submission $submission, Request $r, SubmissionStructureService $svc)
    {
        $mode = $r->string('mode')->toString() === 'hard' ? 'hard_reset' : 'create_missing';
        $svc->syncToType($submission, $submission->tipo_trabalho, mode: $mode);

        $msg = $mode === 'hard_reset'
            ? 'Seções padrão reaplicadas. Seções vazias que não pertenciam ao tipo foram removidas.'
            : 'Seções padrão adicionadas/reordenadas. Seu conteúdo foi preservado.';
        return back()->with('ok', $msg);
    }

    public function assetsStore(Request $r, Submission $submission)
    {
        $data = $r->validate([
            'section_id' => ['nullable','integer','exists:submission_sections,id'],
            'type'       => ['required','in:figure,table,attachment'],
            'file'       => ['nullable','file','max:51200'],
            'caption'    => ['nullable','string','max:255'],
            'source'     => ['nullable','string','max:255'],
            'order'      => ['nullable','integer','min:1'],
        ]);

        $disk = config('filesystems.default', 'public');
        if (!array_key_exists($disk, config('filesystems.disks'))) $disk = 'public';
        if ($disk === 'local') $disk = 'public';

        $path = null;
        if ($r->hasFile('file')) {
            $dir  = "submissions/{$submission->id}/assets";
            $path = $r->file('file')->store($dir, $disk);
        }

        \App\Models\SubmissionAsset::create([
            'submission_id' => $submission->id,
            'section_id'    => $data['section_id'] ?? null,
            'type'          => $data['type'],
            'disk'          => $disk,
            'file_path'     => $path,
            'caption'       => $data['caption'] ?? null,
            'source'        => $data['source'] ?? null,
            'order'         => $data['order'] ?? 1,
        ]);

        return back()->with('ok','Anexo adicionado.');
    }

    public function updateConfigs(Request $r, Submission $submission)
    {
        $this->ownsOrAbort($r, $submission);

        $data = $r->validate([
            'numbering_config' => ['nullable','array'],
            'pagination_config' => ['nullable','array'],
        ]);

        $num = array_merge(Submission::defaultNumberingConfig(), $data['numbering_config'] ?? []);
        $pag = array_merge(Submission::defaultPaginationConfig(), $data['pagination_config'] ?? []);

        $submission->numbering_config  = $num;
        $submission->pagination_config = $pag;
        $submission->save();

        return back()->with('ok', 'Configurações de numeração e paginação atualizadas.');
    }

    public function uploadAsset(Submission $submission, Request $request)
    {
        $request->validate([
            'section_id'     => ['nullable','integer'],
            'type'           => ['nullable','in:figure,table,attachment'],
            'file'           => ['nullable','file'],
            'chart_json'     => ['nullable','string'],
            'label'          => ['nullable','string','max:120'],
            'numbering'      => ['nullable','string','max:40'],
            'caption'        => ['nullable','string','max:500'],
            'alt_text'       => ['nullable','string','max:500'],
            'source'         => ['nullable','string','max:255'],
            'order'          => ['nullable','integer','min:1'],
        ]);

        $disk = config('filesystems.default', 'public');
        if (!array_key_exists($disk, config('filesystems.disks'))) {
            $disk = 'public';
        }
        if ($disk === 'local') {
            $disk = 'public';
        }

        $dir  = "submissions/{$submission->id}/assets";
        $path = $mime = null; $size = 0; $type = $request->input('type', 'figure');

        if ($request->filled('chart_json')) {
            $chart = $request->input('chart_json');
            $clean = preg_replace('#/\*.*?\*/#s', '', $chart);
            $clean = preg_replace('#^\s*//.*$#m', '', $clean);
            $clean = trim($clean);
            json_decode($clean);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['chart_json' => 'JSON inválido: '.json_last_error_msg()])->withInput();
            }
            $name = 'chart-'.Str::uuid().'.json';
            Storage::disk($disk)->put("$dir/$name", $clean);
            $path = "$dir/$name";
            $mime = 'application/json';
            $size = strlen($clean);
            $type = 'figure';
        } elseif ($file = $request->file('file')) {

            if (!Storage::disk($disk)->exists($dir)) {
                Storage::disk($disk)->makeDirectory($dir);
            }
            $path = $file->store($dir, $disk);
            $mime = $file->getClientMimeType();
            $size = $file->getSize();
            $type = $request->input('type','figure');
        } else {
            return back()->withErrors(['file' => 'Envie um arquivo ou cole um JSON de gráfico.'])->withInput();
        }

        \App\Models\SubmissionAsset::create([
            'submission_id' => $submission->id,
            'section_id'    => $request->integer('section_id') ?: null,
            'type'          => $type,
            'disk'          => $disk,
            'file_path'     => $path,
            'mime'          => $mime,
            'size'          => $size,
            'label'         => (string)$request->input('label'),
            'numbering'     => (string)$request->input('numbering'),
            'caption'       => (string)$request->input('caption'),
            'alt_text'      => (string)$request->input('alt_text'),
            'source'        => (string)$request->input('source'),
            'order'         => max(1, (int)$request->input('order', 1)),
        ]);

        return back()->with('ok','Anexo salvo com sucesso.');
    }


    public function destroyAsset(Request $r, Submission $submission, SubmissionAsset $asset)
    {
        $this->ownsOrAbort($r, $submission);
        if ($asset->submission_id !== $submission->id) {
            abort(403);
        }

        try {
            if ($asset->disk && $asset->file_path) {
                Storage::disk($asset->disk)->delete($asset->file_path);
            }
        } catch (\Throwable $e) {
        }

        $asset->delete();

        return back()->with('ok', 'Anexo removido.');
    }

    public function storeReference(Request $r, Submission $submission)
    {
        $this->ownsOrAbort($r, $submission);

        $data = $r->validate([
            'raw' => ['required','string','min:5'],
            'doi' => ['nullable','string','max:255'],
            'url' => ['nullable','url','max:2000'],
            'order' => ['nullable','integer','min:1'],
            'citekey' => ['nullable','string','max:120'],
            'accessed_at' => ['nullable','date'],
        ]);

        SubmissionReference::create([
            'submission_id' => $submission->id,
            'order' => $data['order'] ?? ($submission->references()->max('order') + 1),
            'citekey' => $data['citekey'] ?? null,
            'raw' => $data['raw'],
            'doi' => $data['doi'] ?? null,
            'url' => $data['url'] ?? null,
            'accessed_at' => $data['accessed_at'] ?? null,
        ]);

        return back()->with('ok', 'Referência adicionada.');
    }

    public function destroyReference(Request $r, Submission $submission, SubmissionReference $reference)
    {
        $this->ownsOrAbort($r, $submission);
        if ($reference->submission_id !== $submission->id) abort(403);

        $reference->delete();

        return back()->with('ok', 'Referência removida.');
    }

    public function submit(Request $r, Submission $submission, AutoAssignService $auto)
    {
        $this->ownsOrAbort($r, $submission);

        $errors = [];
        if (!$submission->title) $errors[] = 'Título é obrigatório.';
        if (mb_strlen((string) $submission->abstract) < 50) $errors[] = 'Resumo muito curto (mín. 50 caracteres).';

        $total = $submission->sections()->count();
        $filled = $submission->sections()
            ->whereNotNull('content')
            ->whereRaw("length(trim(content)) >= 30")
            ->count();
        if ($total === 0 || $filled < $total) $errors[] = 'Todas as seções devem estar preenchidas.';

        $hasPrimaryCategory = $submission->categories()
            ->wherePivot('is_primary', true)
            ->exists();
        if (!$hasPrimaryCategory) $errors[] = 'Selecione uma categoria (obrigatória).';

        if (!empty($errors)) {
            return back()->withErrors($errors)->with('error', 'Revise os itens antes de enviar.');
        }

        if ($submission->status === \App\Models\Submission::ST_SUBMITTED) {
            return redirect()->route('autor.submissions.index')
                ->with('ok', 'Esta submissão já foi enviada.');
        }

        $assigned = 0;

        DB::transaction(function () use (&$assigned, $submission, $auto) {
            $submission->status       = \App\Models\Submission::ST_SUBMITTED;
            $submission->submitted_at = now();
            $submission->save();

            $alreadyAssigned = $submission->assignments()->exists();
            if (!$alreadyAssigned) {
                $assigned = $auto->assignByPrimaryCategory($submission, 1);
            } else {
                $assigned = $submission->assignments()->count();
            }
        });

        return redirect()->route('autor.submissions.index')
            ->with('ok', $assigned > 0
                ? 'Submissão enviada e atribuída a revisor.'
                : 'Submissão enviada para análise. (Nenhum revisor elegível encontrado para a categoria primária)');
    }


    public function resubmitCorrections(Request $r, Submission $submission)
    {
        $this->ownsOrAbort($r, $submission);

        if ($submission->status !== Submission::ST_REV_REQ) {
            return back()->with('error','Esta submissão não está em correção.');
        }

        $pend = $submission->comments()->where('level','must_fix')->where('status','open')->exists();
        if ($pend) {
            return back()->with('error','Ainda existem pendências bloqueantes (must_fix).');
        }

        DB::transaction(function () use ($submission) {
            $submission->status = Submission::ST_REVIEW;
            if ($submission->isFillable('resubmitted_at')) {
                $submission->resubmitted_at = now();
            }
            $submission->save();
        });

        return redirect()->route('autor.submissions.index')->with('ok','Correções reenviadas ao revisor.');
    }

    public function destroy(Request $r, Submission $submission)
    {
        $this->ownsOrAbort($r, $submission);

        if ($submission->status !== Submission::ST_DRAFT) {
            return back()->with('error', 'Apenas rascunhos podem ser excluídos pelo autor.');
        }

        $submission->delete();

        return redirect()->route('autor.submissions.index')->with('ok', 'Rascunho removido.');
    }

    private function ownsOrAbort(Request $r, Submission $submission): void
    {
        if ($submission->user_id !== $r->user()->id) {
            abort(403);
        }
    }

    private function belongsToOrAbort(SubmissionSection $section, Submission $submission): void
    {
        if ($section->submission_id !== $submission->id) {
            abort(404);
        }
    }

    private function bootstrapSectionsInternal(Submission $sub): void
    {
        $map = match ($sub->tipo_trabalho) {
            Submission::TP_ORIGINAL, Submission::TP_BRIEF => [
                'Introdução','Métodos','Resultados','Discussão','Conclusões','Agradecimentos','Referências',
            ],
            Submission::TP_REV_NARR => [
                'Introdução','Método (busca e síntese)','Síntese da literatura','Discussão','Conclusões','Agradecimentos','Referências',
            ],
            Submission::TP_REV_SIST => [
                'Introdução e objetivo','Métodos (protocolo, busca, critérios, extração, risco de viés)','Resultados (estudos incluídos e síntese)','Discussão','Conclusões','Agradecimentos','Referências',
            ],
            Submission::TP_CASE => [
                'Introdução','Caso/Contexto','Procedimentos/Intervenção','Achados','Discussão','Implicações/Ética','Conclusões','Agradecimentos','Referências',
            ],
            Submission::TP_TECH => [
                'Introdução','Problema e objetivos','Arquitetura/Metodologia','Implementação','Avaliação/Métricas','Discussão/Lições','Conclusions','Agradecimentos','Referências',
            ],
            default => [
                'Introdução','Métodos','Resultados','Discussão','Conclusões','Agradecimentos','Referências',
            ],
        };

        DB::transaction(function () use ($sub, $map) {
            $pos = 1;
            foreach ($map as $title) {
                $showNumber = !in_array($title, ['Agradecimentos','Referências'], true);
                SubmissionSection::create([
                    'submission_id' => $sub->id,
                    'parent_id' => null,
                    'position' => $pos++,
                    'title' => $title,
                    'content' => null,
                    'level' => 1,
                    'numbering' => null,
                    'show_in_toc' => true,
                    'show_number' => $showNumber,
                ]);
            }
        });
    }

    private function neighborSectionIds(Submission $sub, SubmissionSection $current): array
    {
        $ids = $sub->rootSections()->pluck('id')->all();
        $ix  = array_search($current->id, $ids, true);
        if ($ix === false) {
            return [null, null];
        }
        $prevId = $ids[$ix - 1] ?? null;
        $nextId = $ids[$ix + 1] ?? null;

        return [$prevId, $nextId];
    }
}

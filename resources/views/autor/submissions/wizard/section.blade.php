@extends('console.layout-author')
@section('title','Editar Seção — Projeto')
@section('page.title','Editar Seção')

@push('head')
<style>
  .section-shell{display:flex;flex-direction:column;gap:1rem}
  .flash{border-radius:.9rem;border:1px solid var(--line);background:var(--panel);padding:.6rem .8rem;font-size:.8rem}
  .flash-ok{border-color:rgba(16,185,129,.4);background:rgba(16,185,129,.08);color:#047857}
  .flash-err{border-color:rgba(248,113,113,.5);background:rgba(254,226,226,1);color:#b91c1c}
  .flash-warn{border-color:rgba(245,158,11,.4);background:rgba(245,158,11,.08);color:#854d0e}

  .section-grid{display:grid;gap:1rem}
  @media(min-width:1024px){.section-grid{grid-template-columns:minmax(0,2fr) minmax(0,1.2fr)}}

  .section-card{border-radius:1.2rem;border:1px solid var(--line);background:var(--panel);padding:1rem 1.1rem}
  .section-card-main{background:radial-gradient(circle at top left,rgba(251,113,133,.16),transparent 60%),radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 60%),var(--panel)}
  .section-card-title{font-size:1rem;font-weight:700}
  .section-card-sub{font-size:.8rem;color:var(--muted);margin-top:.15rem}
  .section-card-meta{display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.4rem;font-size:.75rem;color:var(--muted)}
  .section-chip{border-radius:999px;padding:.1rem .6rem;border:1px solid rgba(148,163,184,.6);background:rgba(15,23,42,.04);display:inline-flex;align-items:center;gap:.3rem}
  .section-chip-dot{width:.38rem;height:.38rem;border-radius:999px;background:var(--brand)}

  .section-header-row{display:flex;flex-direction:column;gap:.7rem}
  @media(min-width:768px){.section-header-row{flex-direction:row;align-items:flex-start;justify-content:space-between}}

  .section-nav{display:flex;flex-wrap:wrap;gap:.4rem;justify-content:flex-start}
  .is-locked{opacity:.8}

  .trv-input,.trv-select,.trv-textarea{width:100%;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.85rem;padding:.55rem .8rem;font-size:.85rem;transition:box-shadow .15s,border-color .15s,background .15s,color .15s}
  .trv-input:focus,.trv-select:focus,.trv-textarea:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(225,29,72,.22)}
  .trv-label{display:block;font-size:.85rem;font-weight:600;margin-bottom:.25rem}
  .trv-help{font-size:.75rem;color:var(--muted)}

  .btn{border-radius:.85rem;padding:.55rem 1.05rem;font-weight:600;font-size:.82rem;display:inline-flex;align-items:center;gap:.3rem}
  .btn-neutral{border:1px solid var(--line);background:var(--panel);color:var(--text)}
  .btn-neutral:hover{background:rgba(148,163,184,.12)}
  .btn-brand{background:var(--brand);color:#fff;border:none}
  .btn-brand[disabled]{opacity:.6;cursor:not-allowed}
  .btn-brand:hover:not([disabled]){filter:brightness(1.02)}

  .section-actions-row{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.4rem}

  .comment-badge{font-size:.72rem;border-radius:999px;padding:.15rem .55rem;display:inline-flex;align-items:center;gap:.25rem}
  .comment-header{font-size:.74rem;display:flex;align-items:center;justify-content:space-between;gap:.5rem}
  .comment-tag{border-radius:.65rem;padding:.1rem .5rem;font-size:.7rem;border:1px solid rgba(148,163,184,.6);background:rgba(15,23,42,.04)}
  .comment-body{font-size:.82rem;margin-top:.35rem}
  .comment-excerpt{font-size:.75rem;margin-top:.35rem}
  .comment-excerpt blockquote{padding:.3rem .4rem;border-radius:.6rem;border:1px dashed var(--line);background:rgba(15,23,42,.03)}

  .asset-list{margin-top:.75rem;display:flex;flex-direction:column;gap:.5rem}
  .asset-item{display:flex;flex-direction:column;gap:.45rem;border-radius:1rem;border:1px solid var(--line);padding:.55rem .75rem}
  @media(min-width:768px){.asset-item{flex-direction:row;align-items:center;justify-content:space-between}}
  .asset-main{min-width:0}
  .asset-title{font-size:.9rem;font-weight:600}
  .asset-meta{font-size:.75rem;color:var(--muted);margin-top:.15rem}

  .refs-list{margin-top:.75rem;display:flex;flex-direction:column;gap:.55rem}
  .ref-item{display:flex;flex-direction:column;gap:.4rem;border-radius:1rem;border:1px solid var(--line);padding:.6rem .75rem}
  @media(min-width:768px){.ref-item{flex-direction:row;align-items:flex-start;justify-content:space-between}}
  .ref-main{font-size:.82rem;min-width:0}
  .ref-header{font-size:.78rem;font-weight:600;margin-bottom:.15rem}
  .ref-meta{font-size:.7rem;color:var(--muted);margin-top:.2rem}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Str;

  $locked = method_exists($sub,'isLocked') ? $sub->isLocked() : in_array($sub->status, ['aceito','rejeitado'], true);
@endphp

<div class="section-shell">
  @if (session('ok'))
    <div class="flash flash-ok">{{ session('ok') }}</div>
  @endif
  @if (session('error'))
    <div class="flash flash-err">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="flash flash-err">
      <ul class="list-disc ml-4">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if($locked)
    <div class="flash flash-warn">
      Projeto finalizado. Alterações não são permitidas; você pode apenas visualizar o conteúdo.
    </div>
  @endif

  <div class="section-grid">
    <div class="space-y-4">
      <div class="section-card section-card-main {{ $locked ? 'is-locked' : '' }}">
        <div class="section-header-row">
          <div class="min-w-0">
            <div class="section-card-title truncate" title="{{ $sub->title }}">
              {{ $sub->title ?: 'Projeto sem título' }}
            </div>
            <div class="section-card-sub">
              {{ $sub->type_label ?? $sub->tipo_trabalho }} • edição da seção
            </div>
            <div class="section-card-meta">
              <span class="section-chip">
                <span class="section-chip-dot"></span>
                <span>Seção atual: {{ $sec->numbering ? $sec->numbering.' — ' : '' }}{{ $sec->title }}</span>
              </span>
            </div>
          </div>
          <div class="section-nav">
            <a href="{{ route('autor.submissions.wizard',$sub) }}" class="btn btn-neutral">Visão geral</a>
            @if ($prevId)
              <a href="{{ route('autor.submissions.section.edit', [$sub,$prevId]) }}" class="btn btn-neutral">Anterior</a>
            @endif
            @if ($nextId)
              <a href="{{ route('autor.submissions.section.edit', [$sub,$nextId]) }}" class="btn btn-neutral">Próxima</a>
            @endif
          </div>
        </div>

        <fieldset @disabled($locked) class="mt-4">
          <form method="POST" action="{{ route('autor.submissions.section.update', [$sub,$sec]) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" name="numbering" value="{{ old('numbering',$sec->numbering) }}">
            <input type="hidden" name="show_number" value="1">
            <input type="hidden" name="show_in_toc" value="1">

            <div>
              <label class="trv-label" for="sec-title">Título da seção</label>
              <input id="sec-title" name="title" value="{{ old('title',$sec->title) }}" class="trv-input" required>
            </div>

            <div>
              <label class="trv-label" for="sec-content">Conteúdo</label>
              <textarea id="sec-content" name="content" rows="12" class="trv-textarea" placeholder="Escreva o conteúdo da seção..." required>{{ old('content',$sec->content) }}</textarea>
              <p class="trv-help mt-1">Use anexos e referências abaixo para complementar o texto.</p>
            </div>

            <div class="section-actions-row">
              @if ($prevId)
                <button name="nav" value="prev" class="btn btn-neutral">Salvar e voltar</button>
              @endif
              <button name="nav" value="stay" class="btn btn-neutral">Salvar</button>
              @if ($nextId)
                <button name="nav" value="next" class="btn btn-brand">Salvar e próxima</button>
              @endif
            </div>
          </form>
        </fieldset>
      </div>

      @php
        $cSec = $sub->comments()->with('user:id,name')->where('section_id', $sec->id)->where('status','open');
        if (Schema::hasColumn('submission_comments','audience')) {
          $cSec->whereIn('audience', ['author','both']);
        }
        $secComments = $cSec
          ->orderByRaw("CASE level WHEN 'must_fix' THEN 0 WHEN 'should_fix' THEN 1 ELSE 2 END")
          ->orderBy('id','asc')
          ->get();

        $lvlColor = function($lvl){
          return match($lvl){
            'must_fix'   => '#dc2626',
            'should_fix' => '#d97706',
            default      => '#6b7280',
          };
        };
      @endphp

      <div class="section-card {{ $locked ? 'is-locked' : '' }}">
        <div class="section-card-title">Correções do revisor nesta seção</div>
        <div class="section-card-sub">Acompanhe e marque como resolvidas as pendências solicitadas.</div>

        @if($secComments->isEmpty())
          <div class="mt-4 text-sm" style="color:var(--muted)">
            Sem correções pendentes para esta seção.
          </div>
        @else
          <div class="mt-4 space-y-3">
            @foreach($secComments as $c)
              @php
                $closedByAuthor = ($c->status !== 'open') && (($c->closed_by ?? null) === 'author');
                $badgeBg = $closedByAuthor ? 'rgba(16,185,129,.14)' : 'rgba(225,29,72,.12)';
                $badgeTx = $closedByAuthor ? '#16a34a' : '#dc2626';
                $badgeTx2= $closedByAuthor ? 'Corrigido pelo autor' : 'Não corrigido';
              @endphp

              <div class="rounded-xl border p-3" style="border-color:var(--line)">
                <div class="comment-header">
                  <span>
                    <span style="color:{{ $lvlColor($c->level) }};font-weight:700">Comentário do revisor</span>
                    <span style="color:var(--muted)"> • {{ $c->user?->name ?? 'Revisor' }}</span>
                  </span>
                  <span class="comment-tag" style="background:{{ $badgeBg }};color:{{ $badgeTx }};border-color:{{ $badgeTx }}33">
                    Status: {{ $badgeTx2 }}
                  </span>
                </div>

                @if($c->excerpt)
                  <div class="comment-excerpt">
                    <div class="font-semibold">Trecho atual:</div>
                    <blockquote>{!! nl2br(e($c->excerpt)) !!}</blockquote>
                  </div>
                @endif

                @if($c->suggested_text)
                  <div class="comment-body">
                    <div class="font-semibold">Sugestão do revisor:</div>
                    <div class="mt-1 p-2 rounded-md" style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25)">
                      {!! nl2br(e($c->suggested_text)) !!}
                    </div>
                  </div>
                @elseif($c->body)
                  <div class="comment-body">{!! nl2br(e($c->body)) !!}</div>
                @endif

                <fieldset @disabled($locked) class="mt-3">
                  <form method="POST" action="{{ route('comments.author_resolved', [$sub, $c]) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-neutral">Marcar como resolvido</button>
                  </form>
                </fieldset>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <div class="space-y-4">
      <div class="section-card {{ $locked ? 'is-locked' : '' }}">
        <div class="section-card-title">Imagens, tabelas e anexos</div>
        <div class="section-card-sub">Adicione arquivos relacionados a esta seção.</div>

        <fieldset @disabled($locked) class="mt-3">
          <form method="POST" action="{{ route('autor.submissions.assets.store',$sub) }}" enctype="multipart/form-data" class="space-y-3" aria-label="Formulário de anexos">
            @csrf
            <input type="hidden" name="section_id" value="{{ $sec->id }}">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
              <div>
                <label class="trv-label" for="asset-type">Tipo</label>
                <select id="asset-type" name="type" class="trv-select">
                  <option value="figure">Figura (imagem)</option>
                  <option value="table">Tabela</option>
                  <option value="attachment">Anexo</option>
                </select>
              </div>
              <div>
                <label class="trv-label" for="asset-order">Ordem</label>
                <input id="asset-order" type="number" name="order" min="1" value="1" class="trv-input">
              </div>
              <div class="md:col-span-2">
                <label class="trv-label" for="asset-file">Arquivo</label>
                <input id="asset-file" type="file" name="file" class="trv-input" accept="image/*,.pdf,.csv,.xlsx,.xls,.doc,.docx,.zip">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="trv-label" for="asset-caption">Legenda / descrição</label>
                <input id="asset-caption" name="caption" class="trv-input" placeholder="Legenda curta">
              </div>
              <div>
                <label class="trv-label" for="asset-source">Fonte</label>
                <input id="asset-source" name="source" class="trv-input" placeholder="Ex.: base de dados, autor, URL">
              </div>
            </div>

            <div class="flex justify-end">
              <button class="btn btn-neutral">Anexar</button>
            </div>
          </form>
        </fieldset>

        <div class="asset-list">
          @forelse ($assets as $a)
            <div class="asset-item">
              <div class="asset-main">
                <div class="asset-title">
                  {{ ucfirst($a->type) }} @if($a->order) #{{ $a->order }} @endif
                </div>
                @if ($a->caption)
                  <div class="text-sm" style="color:var(--text)">{{ $a->caption }}</div>
                @endif
                @if ($a->file_path)
                  <div class="asset-meta">
                    Arquivo: {{ \Illuminate\Support\Str::afterLast($a->file_path,'/') }}
                  </div>
                @endif
              </div>
              <fieldset @disabled($locked) class="shrink-0">
                <form method="POST" action="{{ route('autor.submissions.assets.destroy', [$sub,$a->id]) }}" onsubmit="return confirm('Remover este anexo?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-neutral">Excluir</button>
                </form>
              </fieldset>
            </div>
          @empty
            <div class="text-sm" style="color:var(--muted)">Nenhum anexo nesta seção.</div>
          @endforelse
        </div>
      </div>

      @php
        $isRef = Str::of($sec->title)->lower()->contains('referenc');
      @endphp

      @if ($isRef)
        <div class="section-card {{ $locked ? 'is-locked' : '' }}">
          <div class="section-card-title">Referências</div>
          <div class="section-card-sub">Cadastre e gerencie as referências bibliográficas deste projeto.</div>

          <fieldset @disabled($locked) class="mt-3">
            <form method="POST" action="{{ route('autor.submissions.refs.store', $sub) }}" class="space-y-3">
              @csrf
              <div>
                <label class="trv-label" for="ref-raw">Referência (texto completo)</label>
                <textarea id="ref-raw" name="raw" rows="3" class="trv-textarea" placeholder="Sobrenome, N. (Ano). Título do trabalho. Revista, volume(número), páginas."></textarea>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                  <label class="trv-label" for="ref-doi">DOI</label>
                  <input id="ref-doi" name="doi" class="trv-input" placeholder="10.xxxx/xyz">
                </div>
                <div>
                  <label class="trv-label" for="ref-url">URL</label>
                  <input id="ref-url" name="url" class="trv-input" placeholder="https://...">
                </div>
                <div>
                  <label class="trv-label" for="ref-order">Ordem</label>
                  <input id="ref-order" type="number" name="order" min="1" value="1" class="trv-input">
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="trv-label" for="ref-citekey">Citekey</label>
                  <input id="ref-citekey" name="citekey" class="trv-input" placeholder="SOBRENOME2025">
                </div>
                <div>
                  <label class="trv-label" for="ref-accessed">Acessado em</label>
                  <input id="ref-accessed" type="date" name="accessed_at" class="trv-input">
                </div>
              </div>
              <div class="flex justify-end">
                <button class="btn btn-neutral">Adicionar referência</button>
              </div>
            </form>
          </fieldset>

          @php
            $refs = $sub->references;
          @endphp

          <div class="refs-list">
            @forelse ($refs as $ref)
              <div class="ref-item">
                <div class="ref-main">
                  <div class="ref-header">[{{ $ref->order }}] {{ $ref->citekey ?? 'sem citekey' }}</div>
                  <div class="break-words">{{ $ref->raw }}</div>
                  @if($ref->doi || $ref->url)
                    <div class="ref-meta">
                      DOI: {{ $ref->doi ?? '—' }} · URL: {{ $ref->url ?? '—' }}
                    </div>
                  @endif
                </div>
                <fieldset @disabled($locked) class="shrink-0">
                  <form method="POST" action="{{ route('autor.submissions.refs.destroy', [$sub,$ref->id]) }}" onsubmit="return confirm('Remover esta referência?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-neutral">Excluir</button>
                  </form>
                </fieldset>
              </div>
            @empty
              <div class="text-sm" style="color:var(--muted)">Nenhuma referência inserida.</div>
            @endforelse
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

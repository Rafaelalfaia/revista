@extends('console.layout-author')
@section('title','Minhas Submissões · Autor')
@section('page.title','Minhas Submissões')

@push('head')
<style>
  .shell-author-sub{display:flex;flex-direction:column;gap:1rem}
  .flash{border-radius:.85rem;border:1px solid var(--line);background:var(--panel);padding:.55rem .75rem;font-size:.8rem}
  .flash-ok{border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.08);color:#047857}
  .flash-warn{border-color:rgba(245,158,11,.4);background:rgba(245,158,11,.08);color:#854d0e}
  .flash-err{border-color:rgba(248,113,113,.5);background:rgba(254,226,226,1);color:#b91c1c}

  .sub-shell-card{border-radius:1.3rem;border:1px solid var(--line);background:radial-gradient(circle at top left,rgba(251,113,133,.16),transparent 55%),radial-gradient(circle at top right,rgba(59,130,246,.2),transparent 55%),var(--panel);overflow:hidden}
  .sub-head{padding:.9rem 1rem 1.1rem;border-bottom:1px solid rgba(148,163,184,.4);display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem}
  .sub-head-left{min-width:0}
  .sub-title{font-size:1.05rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .sub-sub{font-size:.78rem;color:var(--muted);margin-top:.1rem}
  .sub-meta-line{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.35rem;font-size:.75rem;color:var(--muted)}
  .sub-meta-pill{border-radius:999px;padding:.1rem .6rem;background:var(--soft);border:1px solid var(--line);display:inline-flex;align-items:center;gap:.25rem}
  .sub-meta-dot{width:.38rem;height:.38rem;border-radius:999px;background:var(--brand)}
  .sub-head-right{text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:.35rem}
  .sub-required{font-size:.7rem;color:var(--muted)}
  .sub-count{font-size:.75rem;color:var(--muted)}
  .sub-chips{display:flex;flex-wrap:wrap;gap:.25rem;justify-content:flex-end}
  .sub-chip-small{font-size:.7rem;border-radius:999px;padding:.1rem .5rem;background:var(--panel);border:1px solid rgba(148,163,184,.6);color:var(--muted)}
  .btn-primary{border-radius:.9rem;padding:.45rem .95rem;font-size:.8rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;background:var(--brand);color:#fff;border:none;white-space:nowrap}

  .sub-body{padding:.85rem 1rem 1rem;display:flex;flex-direction:column;gap:.7rem}
  .sub-list{display:flex;flex-direction:column;gap:.6rem}
  .sub-card{border-radius:1.1rem;border:1px solid var(--line);background:var(--panel);padding:.7rem .85rem;display:flex;flex-direction:column;gap:.45rem}
  @media(min-width:768px){.sub-card{flex-direction:row;align-items:center;justify-content:space-between;gap:.75rem}}
  .sub-main{min-width:0}
  .sub-header-line{display:flex;align-items:center;gap:.35rem;min-width:0}
  .sub-title-link{font-size:.9rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .sub-type-chip{font-size:.7rem;border-radius:999px;padding:.15rem .55rem;border:1px solid var(--line);background:var(--soft);white-space:nowrap}
  .sub-meta-row{display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.25rem;font-size:.75rem;color:var(--muted)}
  .sub-actions{display:flex;align-items:center;gap:.35rem;flex-wrap:wrap}
  .btn-small-primary{border-radius:.8rem;padding:.35rem .85rem;font-size:.78rem;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;background:#020617;color:#fff;border:none}
  .btn-small-ghost{border-radius:.8rem;padding:.35rem .75rem;font-size:.78rem;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;border:1px solid var(--line);background:var(--panel)}

  .status-pill{border-radius:999px;padding:.15rem .6rem;font-size:.7rem;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;border:1px solid transparent;white-space:nowrap}
  .status-pill-dot{width:.45rem;height:.45rem;border-radius:999px}
  .status-pill.st-rascunho{background:rgba(148,163,184,.12);color:#475569;border-color:rgba(148,163,184,.4)}
  .status-pill.st-submetido{background:rgba(59,130,246,.12);color:#1d4ed8;border-color:rgba(59,130,246,.4)}
  .status-pill.st-em-revisao{background:rgba(59,130,246,.12);color:#1d4ed8;border-color:rgba(59,130,246,.4)}
  .status-pill.st-em-triagem{background:rgba(234,179,8,.12);color:#854d0e;border-color:rgba(234,179,8,.4)}
  .status-pill.st-revisao-solicitada{background:rgba(249,115,22,.15);color:#c2410c;border-color:rgba(249,115,22,.5)}
  .status-pill.st-aceito{background:rgba(16,185,129,.14);color:#047857;border-color:rgba(16,185,129,.5)}
  .status-pill.st-rejeitado{background:rgba(248,113,113,.12);color:#b91c1c;border-color:rgba(248,113,113,.45)}
  .status-pill.st-publicado{background:rgba(37,99,235,.14);color:#1e3a8a;border-color:rgba(37,99,235,.5)}
  .status-pill.st-indefinido{background:rgba(148,163,184,.12);color:#475569;border-color:rgba(148,163,184,.4)}
  .status-pill.st-rascunho .status-pill-dot{background:#64748b}
  .status-pill.st-submetido .status-pill-dot{background:#2563eb}
  .status-pill.st-em-revisao .status-pill-dot{background:#1d4ed8}
  .status-pill.st-em-triagem .status-pill-dot{background:#eab308}
  .status-pill.st-revisao-solicitada .status-pill-dot{background:#f97316}
  .status-pill.st-aceito .status-pill-dot{background:#10b981}
  .status-pill.st-rejeitado .status-pill-dot{background:#ef4444}
  .status-pill.st-publicado .status-pill-dot{background:#2563eb}
  .status-pill.st-indefinido .status-pill-dot{background:#64748b}

  .empty-state{border-radius:1.1rem;border:1px dashed var(--line);padding:2rem 1rem;text-align:center;font-size:.85rem;color:var(--muted);background:var(--panel)}
</style>
@endpush

@section('content')
@php
  use App\Models\Submission;
  $onPage = method_exists($subs,'getCollection') ? $subs->getCollection() : collect($subs);
  $cntDraft     = $onPage->where('status','rascunho')->count();
  $cntSubmitted = $onPage->where('status','submetido')->count();
  $cntPublished = $onPage->where('status','publicado')->count();
@endphp

<div class="shell-author-sub">
  @if (session('ok'))
    <div class="flash flash-ok">{{ session('ok') }}</div>
  @endif
  @if (session('warn'))
    <div class="flash flash-warn">{{ session('warn') }}</div>
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

  <div class="sub-shell-card">
    <div class="sub-head">
      <div class="sub-head-left">
        <div class="sub-title">Minhas submissões</div>
        <div class="sub-sub">Acompanhe todos os artigos que você já iniciou ou enviou para avaliação.</div>
        <div class="sub-meta-line">
          <div class="sub-meta-pill">
            <span class="sub-meta-dot"></span>
            <span>{{ $subs->total() }} registro{{ $subs->total() === 1 ? '' : 's' }} no total</span>
          </div>
        </div>
      </div>
      <div class="sub-head-right">
        <div class="sub-required">Visão por página, inspirada em app.</div>
        <div class="sub-chips">
          <span class="sub-chip-small">Rascunhos: {{ $cntDraft }}</span>
          <span class="sub-chip-small">Submetidas: {{ $cntSubmitted }}</span>
          <span class="sub-chip-small">Publicadas: {{ $cntPublished }}</span>
        </div>
        <a href="{{ route('autor.submissions.create') }}" class="btn-primary">
          Nova submissão
        </a>
      </div>
    </div>

    <div class="sub-body">
      @if ($subs->count())
        <div class="sub-list">
          @foreach ($subs as $s)
            @php
              $statusRaw = $s->status ?? 'rascunho';
              $statusClass = match($statusRaw){
                'revisao_solicitada' => 'st-revisao-solicitada',
                'aceito'             => 'st-aceito',
                'rejeitado'          => 'st-rejeitado',
                'publicado'          => 'st-publicado',
                'submetido'          => 'st-submetido',
                'em_revisao'         => 'st-em-revisao',
                'em_triagem'         => 'st-em-triagem',
                default              => 'st-rascunho',
              };
              $isEditable = in_array($statusRaw, ['rascunho','revisao_solicitada']);
            @endphp
            <div class="sub-card">
              <div class="sub-main">
                <div class="sub-header-line">
                  <a class="sub-title-link hover:text-rose-600"
                     href="{{ route('autor.submissions.wizard',$s) }}"
                     title="{{ $s->title }}">
                    {{ $s->title }}
                  </a>
                  @if($s->type_label)
                    <span class="sub-type-chip">{{ $s->type_label }}</span>
                  @endif
                </div>
                <div class="sub-meta-row">
                  <span class="status-pill {{ $statusClass }}">
                    <span class="status-pill-dot"></span>
                    {{ $s->status_label }}
                  </span>
                  <span>Atualizado {{ $s->updated_at->diffForHumans() }}</span>
                </div>
              </div>

              <div class="sub-actions">
                <a href="{{ route('autor.submissions.wizard',$s) }}" class="btn-small-primary">
                  @if($isEditable)
                    Continuar
                  @else
                    Ver detalhes
                  @endif
                </a>

                @if ($statusRaw === 'rascunho')
                  <form method="POST"
                        action="{{ route('autor.submissions.destroy',$s) }}"
                        onsubmit="return confirm('Excluir este rascunho?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn-small-ghost" type="submit">Excluir</button>
                  </form>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="empty-state">
          Você ainda não possui submissões. Comece criando a sua primeira clicando em “Nova submissão”.
        </div>
      @endif
    </div>
  </div>

  <div class="mt-4">
    {{ $subs->links() }}
  </div>
</div>
@endsection

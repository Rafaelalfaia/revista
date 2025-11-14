@extends('console.layout')

@section('title','Submissões · Coordenador')
@section('page.title','Submissões dos meus revisores')

@php
  use App\Models\Submission;

  $statusMap = [
    Submission::ST_DRAFT     => 'Rascunho',
    Submission::ST_SUBMITTED => 'Submetido',
    Submission::ST_SCREEN    => 'Em triagem',
    Submission::ST_REVIEW    => 'Em revisão',
    Submission::ST_REV_REQ   => 'Revisão solicitada',
    Submission::ST_ACCEPTED  => 'Aceito',
    Submission::ST_PUBLISHED => 'Publicado',
  ];

  $normalizeStatus = function(?string $s){
    return str_replace('_','-', $s ?? 'indefinido');
  };
  $labelStatus = function(?string $s) use ($statusMap){
    return $statusMap[$s] ?? ucwords(str_replace('_',' ', $s ?? 'indefinido'));
  };
@endphp

@push('head')
<style>
  .shell-sub{display:flex;flex-direction:column;gap:1rem}
  .alert-ok,.alert-err{border-radius:1rem;border:1px solid var(--line);padding:.6rem .8rem;font-size:.8rem}
  .alert-ok{background:var(--panel);color:var(--text)}
  .alert-err{background:rgba(248,113,113,.08);color:rgb(248,113,113);border-color:rgba(248,113,113,.6)}
  .filter-card{border-radius:1.1rem;border:1px solid var(--line);background:var(--panel);padding:.65rem .8rem;display:flex;flex-direction:column;gap:.6rem}
  .filter-header{display:flex;align-items:center;justify-content:space-between;gap:.5rem}
  .filter-title{font-size:.85rem;font-weight:600}
  .filter-meta{font-size:.75rem;color:var(--muted)}
  .filter-grid{display:grid;gap:.5rem;margin-top:.2rem}
  @media(min-width:640px){.filter-grid{grid-template-columns:minmax(0,1.5fr) minmax(0,1fr) auto}}
  .input-shell{position:relative;display:flex;align-items:center}
  .input-shell input,.input-shell select{width:100%;border-radius:.85rem;border:1px solid var(--line);background:var(--panel-2);padding:.55rem .75rem;font-size:.8rem}
  .input-shell input[type=search]{padding-left:2rem}
  .input-shell span.icon{position:absolute;left:.55rem;font-size:.9rem;color:var(--muted)}
  .btn-clear{border-radius:.85rem;border:1px solid var(--line);background:var(--panel-2);padding:.5rem .8rem;font-size:.75rem;font-weight:500;white-space:nowrap}
  .list-shell{display:flex;flex-direction:column;gap:.6rem}
  .list-empty{border-radius:1.1rem;border:1px dashed var(--line);padding:1.2rem .9rem;text-align:center;font-size:.8rem;color:var(--muted)}
  .card-submission{border-radius:1.1rem;border:1px solid var(--line);background:var(--panel);padding:.65rem .75rem;display:grid;gap:.4rem}
  .sub-header{display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem}
  .sub-title{font-size:.9rem;font-weight:600;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .sub-meta-row{display:flex;flex-wrap:wrap;gap:.35rem;font-size:.7rem;color:var(--muted);margin-top:.1rem}
  .sub-meta-row span{display:inline-flex;align-items:center;gap:.15rem}
  .sub-body{display:flex;flex-direction:column;gap:.35rem}
  .badge-count{border-radius:999px;padding:.12rem .55rem;font-size:.7rem;background:var(--soft);border:1px solid var(--line)}
  .status-pill{border-radius:999px;padding:.15rem .6rem;font-size:.7rem;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;border:1px solid transparent;white-space:nowrap}
  .status-pill.st-rascunho{background:rgba(148,163,184,.12);color:#475569;border-color:rgba(148,163,184,.4)}
  .status-pill.st-submetido{background:rgba(59,130,246,.12);color:#1d4ed8;border-color:rgba(59,130,246,.35)}
  .status-pill.st-em-triagem{background:rgba(234,179,8,.12);color:#854d0e;border-color:rgba(234,179,8,.4)}
  .status-pill.st-em-revisao{background:rgba(59,130,246,.12);color:#1d4ed8;border-color:rgba(59,130,246,.4)}
  .status-pill.st-revisao-solicitada{background:rgba(249,115,22,.12);color:#c2410c;border-color:rgba(249,115,22,.4)}
  .status-pill.st-aceito{background:rgba(16,185,129,.14);color:#047857;border-color:rgba(16,185,129,.5)}
  .status-pill.st-rejeitado{background:rgba(248,113,113,.12);color:#b91c1c;border-color:rgba(248,113,113,.45)}
  .status-pill.st-publicado{background:rgba(37,99,235,.14);color:#1e3a8a;border-color:rgba(37,99,235,.5)}
  .status-pill.st-indefinido{background:rgba(148,163,184,.12);color:#475569;border-color:rgba(148,163,184,.4)}
  .reviewers-row{display:flex;flex-wrap:wrap;gap:.25rem}
  .reviewer-chip{border-radius:.85rem;border:1px solid var(--line);background:var(--panel-2);padding:.16rem .5rem;font-size:.7rem}
  .sub-footer{display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-top:.2rem}
  .btn-open{border-radius:.8rem;border:1px solid var(--line);background:var(--panel-2);padding:.32rem .8rem;font-size:.75rem;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;white-space:nowrap}
  .pagination-shell{margin-top:1rem}
  @media(min-width:768px){
    .card-submission{padding:.8rem .9rem}
    .sub-header{align-items:center}
  }
</style>
@endpush

@section('content')
<div class="shell-sub">
  @if (session('ok'))
    <div class="alert-ok">{{ session('ok') }}</div>
  @endif
  @if (session('err'))
    <div class="alert-err">{{ session('err') }}</div>
  @endif

  <form method="GET" class="filter-card">
    <div class="filter-header">
      <div>
        <div class="filter-title">Filtrar submissões</div>
        <div class="filter-meta">
          {{ $subs->total() }} resultado{{ $subs->total() === 1 ? '' : 's' }} encontrados
        </div>
      </div>
    </div>
    <div class="filter-grid">
      <div class="input-shell">
        <input type="search" name="q" value="{{ $q }}" placeholder="Buscar por título">
      </div>
      <div class="input-shell">
        <select name="status">
          <option value="">Todos os status</option>
          @foreach ($statusMap as $k => $v)
            <option value="{{ $k }}" {{ $status===$k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn-clear">
        Aplicar filtros
      </button>
    </div>
  </form>

  <div class="list-shell">
    @forelse ($subs as $s)
      @php
        $correcoes = $s->reviews->where('status','revisao_solicitada')->count();
        $statusKey   = $normalizeStatus($s->status);
        $statusLabel = $labelStatus($s->status);
        $updated     = $s->updated_at?->format('d/m/Y H:i');
      @endphp
      <div class="card-submission">
        <div class="sub-header">
          <div class="sub-body">
            <div class="sub-title">{{ $s->title }}</div>
            <div class="sub-meta-row">
              @if($updated)
                <span>
                  <span>Atualizado</span>
                  <span>{{ $updated }}</span>
                </span>
              @endif
              <span>
                <span>Correções</span>
                <span class="badge-count">{{ $correcoes }}</span>
              </span>
            </div>
          </div>
          <span class="status-pill st-{{ $statusKey }}">{{ $statusLabel }}</span>
        </div>

        <div class="reviewers-row">
          @forelse ($s->reviews as $rv)
            <span class="reviewer-chip">{{ $rv->reviewer->name }}</span>
          @empty
            <span class="reviewer-chip" style="opacity:.7">Sem revisores atribuídos</span>
          @endforelse
        </div>

        <div class="sub-footer">
          <div class="sub-meta-row">
            <span>Submissão #{{ $s->id }}</span>
          </div>
          <a href="{{ route('coordenador.submissions.show', $s) }}" class="btn-open">
            <span>Abrir</span>
          </a>
        </div>
      </div>
    @empty
      <div class="list-empty">
        Nenhuma submissão encontrada para seus revisores.
      </div>
    @endforelse
  </div>

  <div class="pagination-shell">
    {{ $subs->links() }}
  </div>
</div>
@endsection

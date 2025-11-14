@extends('console.layout-author')

@section('title','Dashboard do Autor · Trivento')
@section('page.title','Meu painel')

@php
  $me = auth()->user();
  $normalizeStatus = function(?string $s){
    return str_replace('_','-', $s ?? 'indefinido');
  };
  $labelStatus = function(?string $s){
    return ucwords(str_replace('_',' ', $s ?? 'indefinido'));
  };
@endphp

@push('head')
<style>
  .shell-author{display:flex;flex-direction:column;gap:1rem}
  .hero-card{border-radius:1.3rem;border:1px solid var(--line);overflow:hidden;background:radial-gradient(circle at top left,rgba(251,113,133,.15),transparent 55%),radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 55%),var(--panel)}
  .hero-inner{padding:.9rem 1rem 1.1rem;display:flex;align-items:flex-end;justify-content:space-between;gap:.75rem}
  .hero-main{display:flex;flex-direction:column;gap:.25rem;min-width:0}
  .hero-greeting{font-size:.8rem;color:var(--muted)}
  .hero-name{font-size:1rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .hero-sub{font-size:.75rem;color:var(--muted)}
  .hero-actions{display:flex;flex-direction:row;flex-wrap:wrap;gap:.4rem;justify-content:flex-end}
  .btn-primary{border-radius:.85rem;padding:.45rem .85rem;font-size:.8rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;background:var(--brand);color:#fff;border:none;white-space:nowrap}
  .btn-secondary{border-radius:.85rem;padding:.4rem .8rem;font-size:.8rem;font-weight:500;display:inline-flex;align-items:center;gap:.3rem;border:1px solid var(--line);background:var(--panel);white-space:nowrap}
  .hero-metrics{margin-top:.7rem;padding:.55rem .9rem;border-top:1px solid rgba(148,163,184,.4);display:flex;gap:.75rem;font-size:.75rem}
  .hero-metric{flex:1;min-width:0}
  .hero-metric-label{color:var(--muted)}
  .hero-metric-value{font-weight:600;font-size:.95rem}
  .kpi-grid{display:grid;gap:.75rem}
  @media(min-width:640px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(min-width:1024px){.kpi-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
  .kpi-card{border-radius:1rem;border:1px solid var(--line);background:var(--panel);padding:.75rem .9rem;display:grid;gap:.15rem}
  .kpi-label{font-size:.75rem;color:var(--muted)}
  .kpi-value{font-size:1.4rem;font-weight:600}
  .kpi-chip{font-size:.7rem;border-radius:999px;padding:.1rem .6rem;background:var(--soft);border:1px solid var(--line);justify-self:flex-start}
  .section-grid{display:grid;gap:.75rem}
  @media(min-width:1024px){.section-grid{grid-template-columns:2fr 1.4fr}}
  .card{border-radius:1rem;border:1px solid var(--line);background:var(--panel);display:flex;flex-direction:column}
  .card-header{padding:.75rem .9rem;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:.5rem}
  .card-title{font-size:.9rem;font-weight:600}
  .card-sub{font-size:.75rem;color:var(--muted)}
  .card-body{padding:.75rem .9rem}
  .task-list{display:flex;flex-direction:column;gap:.5rem;font-size:.8rem}
  .task-item{display:flex;align-items:center;justify-content:space-between;gap:.4rem}
  .task-title{flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .pill-link{font-size:.75rem;border-radius:.9rem;padding:.25rem .6rem;border:1px solid var(--line);background:var(--panel-2);white-space:nowrap}
  .card-stat-grid{display:flex;gap:.9rem}
  .stat-block{flex:1;min-width:0}
  .stat-label{font-size:.75rem;color:var(--muted)}
  .stat-value{font-size:1.3rem;font-weight:600}
  .stat-note{font-size:.7rem;color:var(--muted);margin-top:.1rem}
  .submissions-card{margin-top:.5rem;border-radius:1.1rem;border:1px solid var(--line);background:var(--panel);overflow:hidden}
  .submissions-header{padding:.75rem .9rem;display:flex;align-items:center;justify-content:space-between;gap:.5rem;border-bottom:1px solid var(--line)}
  .submissions-title{font-size:.9rem;font-weight:600}
  .submissions-body{padding:.65rem .7rem}
  .submission-list{display:flex;flex-direction:column;gap:.55rem}
  .submission-card{border-radius:.85rem;border:1px solid var(--line);background:var(--panel-2);padding:.55rem .65rem;display:flex;align-items:flex-start;justify-content:space-between;gap:.6rem}
  .submission-main{flex:1;min-width:0}
  .submission-title{font-size:.85rem;font-weight:600;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .submission-meta{margin-top:.15rem;font-size:.7rem;color:var(--muted);display:flex;flex-wrap:wrap;gap:.35rem}
  .submission-actions{display:flex;flex-direction:column;align-items:flex-end;gap:.3rem;flex-shrink:0}
  .btn-small{border-radius:.7rem;padding:.25rem .6rem;font-size:.7rem;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;border:1px solid var(--line);background:var(--panel)}
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
  .empty{font-size:.8rem;color:var(--muted);text-align:center;padding:.4rem .3rem}
  .badge-count{border-radius:999px;background:var(--soft);border:1px solid var(--line);padding:.1rem .6rem;font-size:.7rem}
  @media(max-width:639.98px){
    .hero-inner{align-items:flex-start}
    .hero-actions{justify-content:flex-start}
    .card-stat-grid{flex-direction:column}
  }
</style>
@endpush

@section('content')
<div class="shell-author">
  <div class="hero-card">
    <div class="hero-inner">
      <div class="hero-main">
        <div class="hero-greeting">Olá, {{ $me?->name ?? 'Autor(a)' }}</div>
        <div class="hero-name">Seu espaço de autor na Trivento</div>
        <div class="hero-sub">Acompanhe rascunhos, submissões e publicações em um só lugar.</div>
      </div>
      <div class="hero-actions">
        @if (Route::has('autor.submissions.create'))
          <a href="{{ route('autor.submissions.create') }}" class="btn-primary">
            <span>Nova submissão</span>
          </a>
        @endif
        @if (Route::has('autor.submissions.index'))
          <a href="{{ route('autor.submissions.index') }}" class="btn-secondary">
            <span>Minhas submissões</span>
          </a>
        @endif
      </div>
    </div>
    <div class="hero-metrics">
      <div class="hero-metric">
        <div class="hero-metric-label">Total</div>
        <div class="hero-metric-value">{{ $stats['total'] ?? 0 }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Rascunhos</div>
        <div class="hero-metric-value">{{ $stats['rascunho'] ?? 0 }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Publicadas</div>
        <div class="hero-metric-value">{{ $stats['publicado'] ?? 0 }}</div>
      </div>
    </div>
  </div>

  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Rascunhos em edição</div>
      <div class="kpi-value">{{ $stats['rascunho'] ?? 0 }}</div>
      <div class="kpi-chip">Artigos ainda não submetidos</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Submetidas</div>
      <div class="kpi-value">{{ $stats['submetido'] ?? 0 }}</div>
      <div class="kpi-chip">Aguardando triagem editorial</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Em revisão</div>
      <div class="kpi-value">{{ $stats['em_revisao'] ?? ($stats['revisao'] ?? 0) }}</div>
      <div class="kpi-chip">Sob análise dos revisores</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Publicadas</div>
      <div class="kpi-value">{{ $stats['publicado'] ?? 0 }}</div>
      <div class="kpi-chip">Já disponíveis na revista</div>
    </div>
  </div>

  <div class="section-grid">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">A fazer</div>
          <div class="card-sub">Rascunhos e correções pendentes.</div>
        </div>
        @php
          $countRasc = ($rascunhos ?? collect())->count();
          $countCorr = ($paraCorrigir ?? collect())->count();
          $totalPend = $countRasc + $countCorr;
        @endphp
        <span class="badge-count">{{ $totalPend }} pendente{{ $totalPend === 1 ? '' : 's' }}</span>
      </div>
      <div class="card-body">
        <div class="task-list">
          @forelse(($rascunhos ?? collect())->take(3) as $s)
            <div class="task-item">
              <span class="task-title">{{ $s->title }}</span>
              <a href="{{ route('autor.submissions.wizard',$s) }}" class="pill-link">Continuar</a>
            </div>
          @empty
            <div class="empty">Sem rascunhos pendentes.</div>
          @endforelse

          @foreach(($paraCorrigir ?? collect())->take(3) as $s)
            <div class="task-item">
              <span class="task-title">{{ $s->title }}</span>
              <a href="{{ route('autor.submissions.wizard',$s) }}" class="pill-link">Enviar correções</a>
            </div>
          @endforeach

          @if(($rascunhos ?? collect())->count() + ($paraCorrigir ?? collect())->count() > 3)
            <div class="empty">Há mais itens na lista em “Minhas submissões”.</div>
          @endif
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Andamento</div>
          <div class="card-sub">Visão rápida das decisões editoriais.</div>
        </div>
      </div>
      <div class="card-body">
        <div class="card-stat-grid">
          <div class="stat-block">
            <div class="stat-label">Em revisão</div>
            <div class="stat-value">{{ $stats['em_revisao'] ?? ($stats['revisao'] ?? 0) }}</div>
            <div class="stat-note">Aguardando pareceres.</div>
          </div>
          <div class="stat-block">
            <div class="stat-label">Aceitas</div>
            <div class="stat-value">{{ $stats['aceito'] ?? 0 }}</div>
            <div class="stat-note">Aprovadas para publicação.</div>
          </div>
        </div>
        <div class="card-stat-grid" style="margin-top:.6rem">
          <div class="stat-block">
            <div class="stat-label">Rejeitadas</div>
            <div class="stat-value">{{ $stats['rejeitado'] ?? 0 }}</div>
            <div class="stat-note">Não seguiram no fluxo.</div>
          </div>
          <div class="stat-block">
            <div class="stat-label">Correções solicitadas</div>
            <div class="stat-value">{{ $stats['revisao_solicitada'] ?? 0 }}</div>
            <div class="stat-note">Aguardando nova versão.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="submissions-card">
    <div class="submissions-header">
      <div>
        <div class="submissions-title">Últimas submissões</div>
        <div class="card-sub">Histórico recente das suas submissões.</div>
      </div>
      @if (Route::has('autor.submissions.index'))
        <a href="{{ route('autor.submissions.index') }}" class="btn-small">Ver todas</a>
      @endif
    </div>
    <div class="submissions-body">
      @php $listaRecentes = $recentes ?? collect(); @endphp
      @if($listaRecentes->isEmpty())
        <div class="empty">Nada por aqui ainda. Comece criando uma nova submissão.</div>
      @else
        <div class="submission-list">
          @foreach($listaRecentes as $s)
            @php
              $statusKey = $normalizeStatus($s->status);
              $statusLabel = $labelStatus($s->status);
              $created = $s->created_at?->format('d/m/Y H:i');
              $isEditable = in_array($s->status, ['rascunho','revisao_solicitada']);
            @endphp
            <div class="submission-card">
              <div class="submission-main">
                <div class="submission-title">{{ $s->title }}</div>

              </div>
              <div class="submission-actions">
                <span class="status-pill st-{{ $statusKey }}">{{ $statusLabel }}</span>
                @if($isEditable)
                  <a href="{{ route('autor.submissions.wizard',$s) }}" class="btn-small">Editar</a>
                @else
                  <a href="{{ route('autor.submissions.wizard',$s) }}" class="btn-small">Ver detalhes</a>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

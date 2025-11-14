@extends('console.layout')

@section('title','Dashboard do Coordenador · Trivento')
@section('page.title','Meu painel')

@php
  $me = auth()->user();
  $normalizeStatus = function(?string $s){
    return str_replace('_','-', $s ?? 'indefinido');
  };
  $labelStatus = function(?string $s){
    return ucwords(str_replace('_',' ', $s ?? 'indefinido'));
  };

  $stats = $stats ?? [];
  $total          = $stats['total']            ?? 0;
  $triagem        = $stats['triagem']          ?? ($stats['em_triagem'] ?? 0);
  $emRevisao      = $stats['revisao']          ?? ($stats['em_revisao'] ?? 0);
  $publicado      = $stats['publicado']        ?? 0;
  $revisaoSolic   = $stats['revisao_solicitada'] ?? 0;
  $aceito         = $stats['aceito']           ?? 0;
  $rejeitado      = $stats['rejeitado']        ?? 0;

  $revisoresCount  = $revisoresCount ?? 0;
  $mediaPorRevisor = $revisoresCount > 0
    ? number_format(($emRevisao / max(1,$revisoresCount)), 1, ',', '.')
    : '0,0';

  $filaTriagem  = $filaTriagem  ?? collect();
  $recentes     = $recentes     ?? collect();

  $totalPendTriagem = $triagem + $revisaoSolic;
@endphp

@push('head')
<style>
  .shell-coord{display:flex;flex-direction:column;gap:1rem}
  .hero-card{border-radius:1.3rem;border:1px solid var(--line);overflow:hidden;background:
    radial-gradient(circle at top left,rgba(52,211,153,.16),transparent 55%),
    radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 55%),
    var(--panel)}
  .hero-inner{padding:.9rem 1rem 1.1rem;display:flex;align-items:flex-end;justify-content:space-between;gap:.75rem}
  .hero-main{display:flex;flex-direction:column;gap:.25rem;min-width:0}
  .hero-greeting{font-size:.8rem;color:var(--muted)}
  .hero-name{font-size:1rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .hero-sub{font-size:.75rem;color:var(--muted)}
  .hero-actions{display:flex;flex-wrap:wrap;gap:.4rem;justify-content:flex-end;align-items:center}
  .btn-primary{border-radius:.85rem;padding:.45rem .85rem;font-size:.8rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;background:var(--brand);color:#fff;border:none;white-space:nowrap}
  .btn-secondary{border-radius:.85rem;padding:.4rem .8rem;font-size:.8rem;font-weight:500;display:inline-flex;align-items:center;gap:.3rem;border:1px solid var(--line);background:var(--panel);white-space:nowrap}
  .badge-role{border-radius:999px;padding:.18rem .6rem;font-size:.7rem;border:1px solid rgba(148,163,184,.5);background:var(--panel-2);color:var(--muted);white-space:nowrap}
  .hero-metrics{margin-top:.7rem;padding:.55rem .9rem;border-top:1px solid rgba(148,163,184,.4);display:flex;gap:.75rem;font-size:.75rem;flex-wrap:wrap}
  .hero-metric{flex:1;min-width:6rem}
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
  .badge-count{border-radius:999px;background:var(--soft);border:1px solid var(--line);padding:.1rem .6rem;font-size:.7rem}
  .empty{font-size:.8rem;color:var(--muted);text-align:center;padding:.4rem .3rem}

  .card-stat-grid{display:flex;flex-direction:column;gap:.9rem}
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

  @media(max-width:767.98px){
    .hero-inner{flex-direction:column;align-items:flex-start}
    .hero-actions{justify-content:flex-start}
  }
  @media(min-width:768px){
    .card-stat-grid{flex-direction:row}
  }
</style>
@endpush

@section('content')
<div class="shell-coord">
  <div class="hero-card">
    <div class="hero-inner">
      <div class="hero-main">
        <div class="hero-greeting">Olá, {{ $me?->name ?? 'Coordenador(a)' }}</div>
        <div class="hero-name">Painel editorial da Trivento</div>
        <div class="hero-sub">Acompanhe triagem, revisões e sua equipe de revisores em um só lugar.</div>
      </div>
      <div class="hero-actions">
        @if (Route::has('coordenador.submissions.index'))
          <a href="{{ route('coordenador.submissions.index') }}" class="btn-primary">
            <span>Ver submissões</span>
          </a>
        @endif
        @if (Route::has('coordenador.relatorios.revisores.index'))
          <a href="{{ route('coordenador.relatorios.revisores.index') }}" class="btn-secondary">
            <span>Relatórios</span>
          </a>
        @endif
        <span class="badge-role">Coordenador(a)</span>
      </div>
    </div>
    <div class="hero-metrics">
      <div class="hero-metric">
        <div class="hero-metric-label">Total</div>
        <div class="hero-metric-value">{{ $total }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Em triagem</div>
        <div class="hero-metric-value">{{ $triagem }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Em revisão</div>
        <div class="hero-metric-value">{{ $emRevisao }}</div>
      </div>
      <div class="hero-metric">
        <div class="hero-metric-label">Publicados</div>
        <div class="hero-metric-value">{{ $publicado }}</div>
      </div>
    </div>
  </div>

  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Submissões em triagem</div>
      <div class="kpi-value">{{ $triagem }}</div>
      <div class="kpi-chip">Aguardando decisão inicial</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Em revisão</div>
      <div class="kpi-value">{{ $emRevisao }}</div>
      <div class="kpi-chip">Sob análise dos revisores</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Correções solicitadas</div>
      <div class="kpi-value">{{ $revisaoSolic }}</div>
      <div class="kpi-chip">Aguardando nova versão dos autores</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Publicados</div>
      <div class="kpi-value">{{ $publicado }}</div>
      <div class="kpi-chip">Já disponíveis na revista</div>
    </div>
  </div>

  <div class="section-grid">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Fila editorial</div>
          <div class="card-sub">Triagem, revisões e correções pendentes.</div>
        </div>
        <span class="badge-count">{{ $totalPendTriagem }} pendência{{ $totalPendTriagem === 1 ? '' : 's' }}</span>
      </div>
      <div class="card-body">
        <div class="task-list">
          <div class="task-item">
            <span class="task-title">Submissões em triagem</span>
            @if (Route::has('coordenador.submissions.index') && $triagem > 0)
              <a href="{{ route('coordenador.submissions.index',['status'=>'em_triagem']) }}" class="pill-link">
                Ver {{ $triagem }}
              </a>
            @else
              <span class="pill-link" style="opacity:.7">Nenhuma pendência</span>
            @endif
          </div>

          <div class="task-item">
            <span class="task-title">Correções a decidir</span>
            @if (Route::has('coordenador.submissions.index') && $revisaoSolic > 0)
              <a href="{{ route('coordenador.submissions.index',['status'=>'revisao_solicitada']) }}" class="pill-link">
                Ver {{ $revisaoSolic }}
              </a>
            @else
              <span class="pill-link" style="opacity:.7">Nenhuma correção aguardando</span>
            @endif
          </div>

          <div class="task-item">
            <span class="task-title">Submissões em revisão</span>
            @if (Route::has('coordenador.submissions.index') && $emRevisao > 0)
              <a href="{{ route('coordenador.submissions.index',['status'=>'em_revisao']) }}" class="pill-link">
                Ver {{ $emRevisao }}
              </a>
            @else
              <span class="pill-link" style="opacity:.7">Sem artigos em revisão</span>
            @endif
          </div>

          @forelse($filaTriagem->take(3) as $s)
            @php
              $statusKey = $normalizeStatus($s->status);
              $statusLabel = $labelStatus($s->status);
            @endphp
            <div class="task-item">
              <span class="task-title">{{ $s->title }}</span>
              <span class="pill-link">
                {{ $statusLabel }}
              </span>
            </div>
          @empty
          @endforelse

          @if($filaTriagem->count() > 3)
            <div class="empty">Há mais itens na fila em “Submissões”.</div>
          @endif
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Equipe de revisores</div>
          <div class="card-sub">Capacidade de revisão e carga atual.</div>
        </div>
      </div>
      <div class="card-body">
        <div class="card-stat-grid">
          <div class="stat-block">
            <div class="stat-label">Revisores cadastrados</div>
            <div class="stat-value">{{ $revisoresCount }}</div>
            <div class="stat-note">Criados por você.</div>
          </div>
          <div class="stat-block">
            <div class="stat-label">Artigos em revisão</div>
            <div class="stat-value">{{ $emRevisao }}</div>
            <div class="stat-note">Distribuídos entre a equipe.</div>
          </div>
        </div>
        <div class="card-stat-grid" style="margin-top:.6rem">
          <div class="stat-block">
            <div class="stat-label">Média por revisor</div>
            <div class="stat-value">{{ $mediaPorRevisor }}</div>
            <div class="stat-note">Submissões por revisor ativo.</div>
          </div>
          <div class="stat-block">
            <div class="stat-label">Decisões recentes</div>
            <div class="stat-value">{{ $aceito + $rejeitado }}</div>
            <div class="stat-note">Aceitas e rejeitadas no período.</div>
          </div>
        </div>

        <div class="task-list" style="margin-top:.8rem">
          <div class="task-item">
            <span class="task-title">Gerenciar revisores</span>
            @if (Route::has('coordenador.revisores.index'))
              <a href="{{ route('coordenador.revisores.index') }}" class="pill-link">Abrir lista</a>
            @else
              <span class="pill-link" style="opacity:.7">Rota de revisores não configurada</span>
            @endif
          </div>
          @if (Route::has('coordenador.relatorios.revisores.index'))
            <div class="task-item">
              <span class="task-title">Relatórios de desempenho</span>
              <a href="{{ route('coordenador.relatorios.revisores.index') }}" class="pill-link">Ver relatórios</a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="submissions-card">
    <div class="submissions-header">
      <div>
        <div class="submissions-title">Últimas movimentações</div>
        <div class="card-sub">Submissões mais recentes sob sua coordenação.</div>
      </div>
      @if (Route::has('coordenador.submissions.index'))
        <a href="{{ route('coordenador.submissions.index') }}" class="btn-small">Ver todas</a>
      @endif
    </div>
    <div class="submissions-body">
      @if($recentes->isEmpty())
        <div class="empty">Nada por aqui ainda. Acompanhe as próximas submissões.</div>
      @else
        <div class="submission-list">
          @foreach($recentes as $s)
            @php
              $statusKey   = $normalizeStatus($s->status);
              $statusLabel = $labelStatus($s->status);
              $data        = $s->submitted_at ?? $s->created_at;
              $dataFmt     = $data ? $data->format('d/m/Y H:i') : null;
            @endphp
            <div class="submission-card">
              <div class="submission-main">
                <div class="submission-title">{{ $s->title }}</div>
                <div class="submission-meta">
                  @if($dataFmt)
                    <span>Atualizado em {{ $dataFmt }}</span>
                  @endif
                  @if(!empty($s->author_name))
                    <span>Autor: {{ $s->author_name }}</span>
                  @endif
                </div>
              </div>
              <div class="submission-actions">
                <span class="status-pill st-{{ $statusKey }}">{{ $statusLabel }}</span>
                @if (Route::has('coordenador.submissions.index'))
                  <a href="{{ route('coordenador.submissions.index',['id'=>$s->id]) }}" class="btn-small">
                    Ver na fila
                  </a>
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

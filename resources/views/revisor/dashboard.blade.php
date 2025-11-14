@extends('console.layout')
@section('title','Dashboard · Revisor')
@section('page.title','Painel do Revisor')

@push('head')
<style>
  .rev-shell{max-width:1100px;margin:0 auto;padding:.75rem 0 0}
  .rev-kpis{
    display:grid;
    gap:.75rem;
    grid-template-columns:minmax(0,1fr);
  }
  @media(min-width:520px){
    .rev-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}
  }
  @media(min-width:900px){
    .rev-kpis{grid-template-columns:repeat(5,minmax(0,1fr))}
  }

  .rev-kpi{
    border:1px solid var(--line);
    background:var(--panel);
    border-radius:1.1rem;
    padding:.7rem .85rem .85rem;
    display:flex;
    flex-direction:column;
    gap:.25rem;
  }
  .rev-kpi-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.5rem;
  }
  .rev-kpi-label{
    font-size:.7rem;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:var(--muted);
  }
  .rev-kpi-badge{
    font-size:.68rem;
    padding:.16rem .6rem;
    border-radius:999px;
    background:var(--chip);
    white-space:nowrap;
  }
  .rev-kpi-value{
    font-size:1.5rem;
    font-weight:700;
    margin-top:.15rem;
  }

  .rev-grid-main{
    display:grid;
    gap:1rem;
    margin-top:1rem;
  }
  @media(min-width:960px){
    .rev-grid-main{grid-template-columns:minmax(0,1.1fr) minmax(0,1fr)}
  }

  .rev-panel{
    border:1px solid var(--line);
    background:var(--panel);
    border-radius:1.25rem;
    padding:1rem;
  }
  @media(min-width:640px){
    .rev-panel{padding:1.1rem 1.25rem}
  }
  .rev-panel-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.75rem;
    margin-bottom:.85rem;
  }
  .rev-panel-title{
    font-weight:600;
    font-size:.98rem;
  }
  .rev-panel-sub{
    font-size:.8rem;
    color:var(--muted);
  }

  .rev-chip-soft{
    border-radius:999px;
    padding:.18rem .7rem;
    font-size:.72rem;
    background:var(--chip);
  }
  .rev-list{
    display:flex;
    flex-direction:column;
    gap:.6rem;
  }
  .rev-row{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:.75rem;
    padding:.75rem;
    border-radius:.9rem;
    border:1px solid var(--line);
    background:var(--panel-2);
  }
  @media(max-width:520px){
    .rev-row{
      flex-direction:column;
      align-items:flex-start;
    }
    .rev-row-right{
      align-items:flex-start;
    }
  }
  .rev-row-main{
    min-width:0;
    display:flex;
    flex-direction:column;
    gap:.15rem;
  }
  .rev-row-title{
    font-size:.9rem;
    font-weight:500;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .rev-row-meta{
    font-size:.75rem;
    color:var(--muted);
  }
  .rev-row-right{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    gap:.3rem;
  }

  .rev-status-pill{
    border-radius:999px;
    padding:.15rem .7rem;
    font-size:.72rem;
    white-space:nowrap;
    background:var(--chip);
  }
  .rev-status-atribuida{background:rgba(59,130,246,.12)}
  .rev-status-em_revisao{background:rgba(52,211,153,.14)}
  .rev-status-revisao_solicitada{background:rgba(251,146,60,.14)}
  .rev-status-parecer_enviado{background:rgba(129,140,248,.16)}
  .dark .rev-status-atribuida{background:rgba(59,130,246,.24)}
  .dark .rev-status-em_revisao{background:rgba(52,211,153,.26)}
  .dark .rev-status-revisao_solicitada{background:rgba(251,146,60,.26)}
  .dark .rev-status-parecer_enviado{background:rgba(129,140,248,.3)}

  .rev-empty{
    font-size:.85rem;
    color:var(--muted);
    padding:.75rem;
    border-radius:.9rem;
    border:1px dashed var(--line);
    background:var(--panel);
  }
</style>
@endpush

@section('page.actions')
  @if(Route::has('revisor.reviews.index'))
    <a href="{{ route('revisor.reviews.index') }}"
       class="inline-flex items-center rounded-lg px-3 h-9 text-sm font-semibold text-white brand">
      Minhas revisões
    </a>
  @endif
@endsection

@section('content')
@php
  $labels = [
    'atribuida'          => 'Atribuída',
    'em_revisao'         => 'Em revisão',
    'revisao_solicitada' => 'Correções solicitadas',
    'parecer_enviado'    => 'Parecer enviado',
  ];
@endphp

<div class="rev-shell space-y-4 sm:space-y-5">

  <div class="rev-kpis">
    <div class="rev-kpi">
      <div class="rev-kpi-top">
        <span class="rev-kpi-label">Total de revisões</span>
        <span class="rev-kpi-badge">Geral</span>
      </div>
      <div class="rev-kpi-value">{{ $totais['total'] }}</div>
    </div>

    <div class="rev-kpi">
      <div class="rev-kpi-top">
        <span class="rev-kpi-label">Atribuídas</span>
        <span class="rev-kpi-badge">Fila</span>
      </div>
      <div class="rev-kpi-value">{{ $totais['atribuida'] }}</div>
    </div>

    <div class="rev-kpi">
      <div class="rev-kpi-top">
        <span class="rev-kpi-label">Em revisão</span>
        <span class="rev-kpi-badge">Em andamento</span>
      </div>
      <div class="rev-kpi-value">{{ $totais['em_revisao'] }}</div>
    </div>

    <div class="rev-kpi">
      <div class="rev-kpi-top">
        <span class="rev-kpi-label">Correções solicitadas</span>
        <span class="rev-kpi-badge">Autor</span>
      </div>
      <div class="rev-kpi-value">{{ $totais['revisao_solicitada'] }}</div>
    </div>

    <div class="rev-kpi">
      <div class="rev-kpi-top">
        <span class="rev-kpi-label">Parecer enviado</span>
        <span class="rev-kpi-badge">Concluídas</span>
      </div>
      <div class="rev-kpi-value">{{ $totais['parecer_enviado'] }}</div>
    </div>
  </div>

  <div class="rev-grid-main">
    <section class="rev-panel">
      <div class="rev-panel-header">
        <div>
          <div class="rev-panel-title">Suas tarefas de revisão</div>
          <div class="rev-panel-sub">Artigos que precisam da sua análise ou acompanhamento.</div>
        </div>
        @if(Route::has('revisor.reviews.index'))
          <a href="{{ route('revisor.reviews.index') }}"
             class="hidden sm:inline-flex items-center rounded-lg px-3 h-8 text-xs font-medium text-white brand">
            Ver todas
          </a>
        @endif
      </div>

      @if($recentes->count())
        <div class="rev-list">
          @foreach($recentes as $r)
            @php
              $s = $r->submission;
              $statusKey = $r->status;
              $statusText = $labels[$statusKey] ?? ucfirst(str_replace('_',' ',$statusKey));
            @endphp
            <div class="rev-row">
              <div class="rev-row-main">
                <div class="rev-row-title" title="{{ $s?->title }}">
                  {{ $s?->title ?? 'Submissão' }}
                </div>
                <div class="rev-row-meta">
                  Atualizado {{ $r->updated_at?->diffForHumans() }}
                </div>
              </div>
              <div class="rev-row-right">
                <span class="rev-status-pill {{ 'rev-status-'.$statusKey }}">
                  {{ $statusText }}
                </span>
                @if(Route::has('revisor.reviews.show'))
                  <a href="{{ route('revisor.reviews.show',$r->id) }}"
                     class="inline-flex items-center rounded-lg px-3 h-8 text-xs font-medium border border-[var(--line)] bg-[var(--panel)]">
                    Abrir
                  </a>
                @elseif(Route::has('revisor.reviews.index'))
                  <a href="{{ route('revisor.reviews.index') }}"
                     class="inline-flex items-center rounded-lg px-3 h-8 text-xs font-medium border border-[var(--line)] bg-[var(--panel)]">
                    Abrir
                  </a>
                @else
                  <span class="rev-row-meta">Ação indisponível</span>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="rev-empty">
          Nenhuma revisão atribuída no momento. Assim que o coordenador enviar novos trabalhos, eles aparecerão aqui.
        </div>
      @endif
    </section>

    <section class="rev-panel">
      <div class="rev-panel-header">
        <div>
          <div class="rev-panel-title">Atualizações recentes</div>
          <div class="rev-panel-sub">Histórico das últimas movimentações nas suas revisões.</div>
        </div>
      </div>

      @if($recentes->count())
        <div class="rev-list">
          @foreach($recentes as $r)
            @php
              $s = $r->submission;
              $statusKey = $r->status;
              $statusText = $labels[$statusKey] ?? ucfirst(str_replace('_',' ',$statusKey));
            @endphp
            <div class="rev-row">
              <div class="rev-row-main">
                <div class="rev-row-title" title="{{ $s?->title }}">
                  {{ $s?->title ?? 'Submissão' }}
                </div>
                <div class="rev-row-meta">
                  Status: {{ $statusText }} • {{ $r->updated_at?->diffForHumans() }}
                </div>
              </div>
              <div class="rev-row-right">

              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="rev-empty">
          Ainda não há histórico recente. Assim que houver movimentações, você verá um timeline aqui.
        </div>
      @endif
    </section>
  </div>
</div>
@endsection

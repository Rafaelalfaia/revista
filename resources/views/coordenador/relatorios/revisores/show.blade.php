@extends('console.layout')
@section('title','Revisor · Indicadores')
@section('page.title','Indicadores do Revisor')

@push('head')
<style>
  .rel2-shell{max-width:1100px;margin:0 auto;padding:1rem;display:grid;gap:1.1rem}
  .rel2-toolbar{border:1px solid var(--line);background:var(--panel);border-radius:1rem;padding:.8rem 1rem;display:flex;gap:.6rem;flex-wrap:wrap;justify-content:space-between;align-items:center}
  .rel2-filters{display:flex;gap:.5rem;flex-wrap:wrap;align-items:center}
  .rel2-input,.rel2-select{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.7rem;padding:.55rem .7rem;font-size:.9rem}
  .rel2-input-date{min-width:150px}
  .rel2-input-search{min-width:220px}
  .rel2-btn{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.8rem;padding:.55rem .9rem;font-weight:600;display:inline-flex;align-items:center;gap:.4rem;white-space:nowrap}
  .rel2-btn-brand{background:var(--brand);color:#fff;border-color:transparent}
  .rel2-card{border:1px solid var(--line);background:var(--panel);border-radius:1.1rem;padding:1rem}
  .rel2-profile{display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap}
  .rel2-profile-main{display:flex;align-items:center;gap:.8rem}
  .rel2-avatar{width:44px;height:44px;border-radius:999px;background:var(--surface);border:1px solid var(--line);display:grid;place-items:center;font-weight:800;font-size:.9rem}
  .rel2-label{font-size:.75rem;color:var(--muted);letter-spacing:.08em;text-transform:uppercase}
  .rel2-name{font-size:1.05rem;font-weight:800}
  .rel2-email{font-size:.85rem;color:var(--muted)}
  .rel2-tabs{display:flex;gap:.5rem;background:var(--surface);border:1px solid var(--line);padding:.35rem;border-radius:.9rem}
  .rel2-tab{padding:.45rem .8rem;border-radius:.65rem;font-weight:600;font-size:.85rem}
  .rel2-tab.active{background:var(--panel);border:1px solid var(--line)}
  .rel2-kpis{display:grid;gap:.8rem;grid-template-columns:repeat(2,minmax(0,1fr))}
  .rel2-kpi{border-radius:1rem;border:1px solid var(--line);background:var(--panel);padding:.8rem .9rem;display:grid;gap:.2rem}
  .rel2-kpi-t{font-size:.76rem;color:var(--muted)}
  .rel2-kpi-v{font-weight:800;font-size:1.5rem}
  @media(min-width:960px){.rel2-kpis{grid-template-columns:repeat(5,minmax(0,1fr))}}
  .rel2-grid2{display:grid;gap:1rem}
  @media(min-width:980px){.rel2-grid2{grid-template-columns:1fr 1fr}}
  .rel2-section{display:grid;gap:.75rem}
  .rel2-section-title{font-weight:700;font-size:.95rem}
  .rel2-list{display:grid;gap:.6rem}
  .rel2-row{display:flex;align-items:center;justify-content:space-between;gap:.9rem;border:1px solid var(--line);background:var(--surface);border-radius:.9rem;padding:.7rem .9rem}
  .rel2-meta{display:flex;flex-direction:column;gap:.18rem;min-width:0}
  .rel2-title{font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .rel2-sub{font-size:.75rem;color:var(--muted)}
  .rel2-chip{background:var(--soft);border:1px solid var(--line);padding:.15rem .55rem;border-radius:.6rem;font-size:.75rem;white-space:nowrap}
  .rel2-muted{color:var(--muted);font-size:.82rem}
  .rel2-pagination{margin-top:.4rem}
</style>
<script>
  document.addEventListener('DOMContentLoaded',function(){
    var tabs=Array.prototype.slice.call(document.querySelectorAll('[data-tab]'));
    var panels=Array.prototype.slice.call(document.querySelectorAll('[data-panel]'));
    var setTab=function(key){
      tabs.forEach(function(t){t.classList.toggle('active',t.dataset.tab===key);});
      panels.forEach(function(p){p.style.display=p.dataset.panel===key?'grid':'none';});
    };
    tabs.forEach(function(t){
      t.addEventListener('click',function(){setTab(t.dataset.tab);});
    });
    setTab('pendentes');
    var search=document.querySelector('#rel2Search');
    if(search){
      var norm=function(s){
        return String(s||'').normalize('NFD').replace(/\p{Diacritic}/gu,'').toLowerCase();
      };
      var rows=Array.prototype.slice.call(document.querySelectorAll('[data-item]'));
      search.addEventListener('input',function(){
        var q=norm(search.value||'');
        rows.forEach(function(r){
          var n=norm(r.dataset.title||'');
          r.style.display=q===''||n.indexOf(q)!==-1?'':'none';
        });
      });
    }
  });
</script>
@endpush

@section('content')
@php
  $nome = $revisor?->name ?? '';
  $parts = preg_split('/\s+/', trim($nome));
  $ini = mb_strtoupper(
    trim(
      (mb_substr($parts[0] ?? '',0,1) ?? '') .
      (mb_substr($parts[1] ?? '',0,1) ?? '')
    )
  );
@endphp

<div class="rel2-shell">
  <form method="GET" class="rel2-toolbar">
    <div class="rel2-filters">
      <input class="rel2-input rel2-input-date" type="date" name="from" value="{{ $from->format('Y-m-d') }}">
      <input class="rel2-input rel2-input-date" type="date" name="to" value="{{ $to->format('Y-m-d') }}">
      @if(!empty($catId))
        <input type="hidden" name="category_id" value="{{ $catId }}">
      @endif
      <button class="rel2-btn rel2-btn-brand" type="submit">Aplicar período</button>
      @if(Route::has('coordenador.relatorios.revisores.index'))
        <a class="rel2-btn" href="{{ route('coordenador.relatorios.revisores.index') }}">Voltar ao painel</a>
      @endif
    </div>
    <input id="rel2Search" class="rel2-input rel2-input-search" placeholder="🔎 Filtrar por título de submissão">
  </form>

  <section class="rel2-card rel2-profile">
    <div class="rel2-profile-main">
      <div class="rel2-avatar">{{ $ini !== '' ? $ini : 'RV' }}</div>
      <div>
        <div class="rel2-label">Revisor</div>
        <div class="rel2-name">{{ $revisor?->name ?? '—' }}</div>
        <div class="rel2-email">{{ $revisor?->email ?? '' }}</div>
      </div>
    </div>
    <div class="rel2-tabs">
      <button type="button" data-tab="pendentes" class="rel2-tab">Fila ativa</button>
      <button type="button" data-tab="concluidas" class="rel2-tab">Concluídas</button>
    </div>
  </section>

  <section class="rel2-kpis">
    <div class="rel2-kpi">
      <div class="rel2-kpi-t">Total de revisões</div>
      <div class="rel2-kpi-v">{{ $stats['total'] }}</div>
    </div>
    <div class="rel2-kpi">
      <div class="rel2-kpi-t">Atribuídas</div>
      <div class="rel2-kpi-v">{{ $stats['atribuida'] }}</div>
    </div>
    <div class="rel2-kpi">
      <div class="rel2-kpi-t">Em revisão</div>
      <div class="rel2-kpi-v">{{ $stats['em_revisao'] }}</div>
    </div>
    <div class="rel2-kpi">
      <div class="rel2-kpi-t">Correções solicitadas</div>
      <div class="rel2-kpi-v">{{ $stats['revisao_solicitada'] }}</div>
    </div>
    <div class="rel2-kpi">
      <div class="rel2-kpi-t">Parecer enviado</div>
      <div class="rel2-kpi-v">{{ $stats['parecer_enviado'] }}</div>
    </div>
  </section>

  <section class="rel2-grid2">
    <div class="rel2-section rel2-card" data-panel="pendentes">
      <div class="rel2-section-title">Fila ativa</div>
      <div class="rel2-list">
        @php $map=['atribuida'=>'Atribuída','em_revisao'=>'Em revisão','revisao_solicitada'=>'Correções solicitadas']; @endphp
        @forelse($pendentes as $rev)
          <div class="rel2-row" data-item data-title="{{ $rev->submission?->title }}">
            <div class="rel2-meta">
              <div class="rel2-title" title="{{ $rev->submission?->title }}">{{ $rev->submission?->title ?? 'Submissão' }}</div>
              <div class="rel2-sub">Criada {{ $rev->created_at->diffForHumans() }}</div>
            </div>
            <span class="rel2-chip">{{ $map[$rev->status] ?? $rev->status }}</span>
          </div>
        @empty
          <p class="rel2-muted">Sem itens pendentes neste período.</p>
        @endforelse
      </div>
      <div class="rel2-pagination">
        {{ $pendentes->onEachSide(1)->links() }}
      </div>
    </div>

    <div class="rel2-section rel2-card" data-panel="concluidas" style="display:none">
      <div class="rel2-section-title">Concluídas no período</div>
      <div class="rel2-list">
        @forelse($concluidas as $rev)
          <div class="rel2-row" data-item data-title="{{ $rev->submission?->title }}">
            <div class="rel2-meta">
              <div class="rel2-title" title="{{ $rev->submission?->title }}">{{ $rev->submission?->title ?? 'Submissão' }}</div>
              <div class="rel2-sub">Atualizado {{ $rev->updated_at->diffForHumans() }}</div>
            </div>
            <span class="rel2-chip">Parecer enviado</span>
          </div>
        @empty
          <p class="rel2-muted">Sem conclusões neste período.</p>
        @endforelse
      </div>
      <div class="rel2-pagination">
        {{ $concluidas->onEachSide(1)->links('pagination::tailwind') }}
      </div>
    </div>
  </section>
</div>
@endsection

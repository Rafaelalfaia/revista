@extends('console.layout')
@section('title','Relatórios · Revisores')
@section('page.title','Indicadores por Revisor')

@push('head')
<style>
  .rel-shell{max-width:1180px;margin:0 auto;padding:1rem;display:grid;gap:1.1rem}
  .rel-hero{border:1px solid var(--line);background:linear-gradient(135deg,var(--panel),rgba(255,255,255,.02));border-radius:1.25rem;padding:1rem;display:grid;gap:.9rem}
  .rel-hero-head{display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap}
  .rel-hero-actions{display:flex;gap:.5rem;flex-wrap:wrap}
  .rel-badge-sla{display:flex;gap:.5rem;align-items:center;font-weight:600;font-size:.9rem}
  .rel-badge-sla-dot{width:.55rem;height:.55rem;border-radius:999px;background:var(--brand)}
  .rel-sla-copy{color:var(--muted);font-size:.9rem}
  .rel-btn{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.9rem;padding:.6rem .95rem;font-weight:700;display:inline-flex;align-items:center;gap:.35rem;white-space:nowrap}
  .rel-btn-brand{background:var(--brand);color:#fff;border-color:transparent}
  .rel-sheet{border:1px solid var(--line);background:var(--panel);border-radius:1rem;padding:.75rem;display:grid;gap:.6rem}
  .rel-filters{display:grid;gap:.6rem;grid-template-columns:repeat(auto-fit,minmax(140px,1fr))}
  .rel-input,.rel-select{width:100%;border:1px solid var(--line);background:var(--surface);color:var(--text);border-radius:.75rem;padding:.6rem .75rem;font-size:.9rem}
  .rel-kpi-scroll{display:flex;gap:.75rem;overflow-x:auto;scroll-snap-type:x mandatory;padding:.1rem .1rem}
  .rel-kpi{min-width:210px;scroll-snap-align:start;border:1px solid var(--line);background:var(--panel);border-radius:1rem;padding:1rem;display:grid;gap:.25rem}
  .rel-kpi-t{font-size:.78rem;color:var(--muted)}
  .rel-kpi-v{font-weight:900;font-size:1.6rem}
  .rel-section-head{display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap}
  .rel-section-title{font-weight:800;font-size:.95rem}
  .rel-section-sub{font-size:.85rem;color:var(--muted)}
  .rel-grid-cards{display:grid;gap:.9rem}
  @media(min-width:960px){.rel-grid-cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
  .rel-card{border:1px solid var(--line);background:var(--panel);border-radius:1rem;padding:1rem;display:grid;gap:.8rem}
  .rel-row{display:flex;align-items:center;justify-content:space-between;gap:.8rem;flex-wrap:wrap}
  .rel-who{display:flex;align-items:center;gap:.75rem;min-width:0}
  .rel-avatar{width:44px;height:44px;border-radius:999px;background:var(--surface);border:1px solid var(--line);display:grid;place-items:center;font-weight:800;font-size:.9rem}
  .rel-name{font-weight:700;line-height:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .rel-email{color:var(--muted);font-size:.9rem;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .rel-pillset{display:flex;gap:.4rem;flex-wrap:wrap}
  .rel-pill{background:var(--surface);border:1px solid var(--line);padding:.22rem .55rem;border-radius:.7rem;font-size:.78rem;font-weight:600;white-space:nowrap}
  .rel-pill-alert{background:rgba(239,68,68,.16);border-color:rgba(248,113,113,.5);color:#fecaca}
  .rel-pill-ok{background:rgba(22,163,74,.14);border-color:rgba(74,222,128,.5)}
  .rel-bar{height:8px;border-radius:999px;background:var(--surface);border:1px solid var(--line);overflow:hidden}
  .rel-bar>i{display:block;height:100%;background:var(--brand)}
  .rel-muted{color:var(--muted);font-size:.8rem}
  .rel-search{min-width:200px}
</style>
<script>
  document.addEventListener('DOMContentLoaded',function(){
    var togg=document.getElementById('relToggleFilters');
    var sheet=document.getElementById('relFiltersSheet');
    if(togg&&sheet){
      togg.addEventListener('click',function(){
        sheet.hidden=!sheet.hidden;
      });
    }
    var input=document.getElementById('relFilterName');
    var cards=Array.prototype.slice.call(document.querySelectorAll('[data-card="rev"]'));
    if(input&&cards.length){
      var norm=function(s){
        return String(s||'').normalize('NFD').replace(/\p{Diacritic}/gu,'').toLowerCase();
      };
      input.addEventListener('input',function(){
        var q=norm(input.value);
        cards.forEach(function(c){
          var n=norm(c.getAttribute('data-name'));
          c.style.display=!q||n.indexOf(q)!==-1?'':'none';
        });
      });
    }
  });
</script>
@endpush

@section('content')
<div class="rel-shell">
  <section class="rel-hero">
    <div class="rel-hero-head">
      <div class="rel-badge-sla">
        <span class="rel-badge-sla-dot"></span>
        <span>Período de revisão: <strong>{{ $sla_dias }} dias</strong></span>
      </div>
      <div class="rel-hero-actions">
        <button id="relToggleFilters" type="button" class="rel-btn">Filtros</button>
        <input id="relFilterName" class="rel-input rel-search" placeholder="Revisor por nome">
      </div>
    </div>

    <p class="rel-sla-copy">
      Prazo-alvo para conclusão de cada revisão a partir da atribuição. Os cards mostram “Fora do Período” quando há pendências acima desse prazo.
    </p>

    <div id="relFiltersSheet" class="rel-sheet" hidden>
      @php $f = $filtros ?? []; @endphp
      <form method="GET" class="rel-filters">
        <input class="rel-input" type="date" name="from" value="{{ optional($f['from'] ?? null)->format('Y-m-d') }}">
        <input class="rel-input" type="date" name="to"   value="{{ optional($f['to']   ?? null)->format('Y-m-d') }}">
        <select name="category_id" class="rel-select">
          <option value="">Todas as áreas</option>
          @foreach($categorias as $c)
            <option value="{{ $c->id }}" @selected(($f['category_id'] ?? null)==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
        <select name="status" class="rel-select">
          @php $st = $f['status'] ?? null; @endphp
          <option value="">Todos status</option>
          <option value="atribuida" @selected($st==='atribuida')>Atribuída</option>
          <option value="em_revisao" @selected($st==='em_revisao')>Em revisão</option>
          <option value="revisao_solicitada" @selected($st==='revisao_solicitada')>Correções solicitadas</option>
          <option value="parecer_enviado" @selected($st==='parecer_enviado')>Parecer enviado</option>
        </select>
        <button class="rel-btn rel-btn-brand" type="submit">Aplicar filtros</button>
      </form>
    </div>

    <div class="rel-kpi-scroll">
      <div class="rel-kpi">
        <div class="rel-kpi-t">Total de revisões</div>
        <div class="rel-kpi-v">{{ $resumo['total'] }}</div>
      </div>
      <div class="rel-kpi">
        <div class="rel-kpi-t">Atribuídas</div>
        <div class="rel-kpi-v">{{ $resumo['atribuida'] }}</div>
      </div>
      <div class="rel-kpi">
        <div class="rel-kpi-t">Em revisão</div>
        <div class="rel-kpi-v">{{ $resumo['em_revisao'] }}</div>
      </div>
      <div class="rel-kpi">
        <div class="rel-kpi-t">Correções solicitadas</div>
        <div class="rel-kpi-v">{{ $resumo['revisao_solicitada'] }}</div>
      </div>
      <div class="rel-kpi">
        <div class="rel-kpi-t">Parecer enviado</div>
        <div class="rel-kpi-v">{{ $resumo['parecer_enviado'] }}</div>
      </div>
    </div>
  </section>

  <div class="rel-section-head">
    <div>
      <div class="rel-section-title">Revisores do período</div>
      <div class="rel-section-sub">Janela de análise de {{ $sla_dias }} dias.</div>
    </div>
  </div>

  <section class="rel-grid-cards">
    @forelse($revisores as $r)
      @php
        $p = $r->total > 0 ? max(0,min(100, round(($r->pendentes / $r->total)*100))) : 0;
        $parts = preg_split('/\s+/', trim($r->nome));
        $ini = mb_strtoupper(
          trim(
            (mb_substr($parts[0] ?? '',0,1) ?? '') .
            (mb_substr($parts[1] ?? '',0,1) ?? '')
          )
        );
      @endphp
      <article class="rel-card" data-card="rev" data-name="{{ $r->nome }}">
        <div class="rel-row">
          <div class="rel-who">
            <div class="rel-avatar">{{ $ini !== '' ? $ini : 'RV' }}</div>
            <div>
              <div class="rel-name">{{ $r->nome }}</div>
              <div class="rel-email">{{ $r->email }}</div>
            </div>
          </div>
          <div class="rel-pillset">
            <span class="rel-pill @if($r->pendentes>0) rel-pill-alert @endif">{{ $r->pendentes }} pendentes</span>
            <span class="rel-pill">{{ $r->throughput_30d }} concluídas em 30d</span>
            <span class="rel-pill">{{ number_format($r->idade_media_pend,1,',','.') }} d idade média</span>
            <span class="rel-pill @if($r->atrasadas_sla>0) rel-pill-alert @else rel-pill-ok @endif">
              Fora do SLA {{ $r->atrasadas_sla }}
            </span>
          </div>
        </div>

        <div class="rel-bar">
          <i style="width:{{ $p }}%"></i>
        </div>

        <div class="rel-row">
          <div class="rel-pillset">
            <span class="rel-pill">Total {{ $r->total }}</span>
            <span class="rel-pill">Atrib. {{ $r->atribuida }}</span>
            <span class="rel-pill">Em rev. {{ $r->em_revisao }}</span>
            <span class="rel-pill">Correções {{ $r->revisao_solicitada }}</span>
            <span class="rel-pill">Pareceres {{ $r->parecer_enviado }}</span>
          </div>
          <div class="rel-muted">
            {{ optional($r->ultima_atividade)->diffForHumans() ?? 'sem atividade recente' }}
          </div>
        </div>

        <div class="rel-row">
          @if(Route::has('coordenador.relatorios.revisores.show'))
            <a class="rel-btn rel-btn-brand" href="{{ route('coordenador.relatorios.revisores.show',$r->reviewer_id) }}">
              Ver detalhes
            </a>
          @else
            <span class="rel-muted">Detalhes indisponíveis.</span>
          @endif
        </div>
      </article>
    @empty
      <p class="rel-muted">Nenhum revisor encontrado no período selecionado.</p>
    @endforelse
  </section>
</div>
@endsection

@php
  use Illuminate\Support\Arr;
  $rows  = collect($data ?? []);
  $total = $rows->sum(fn($r) => is_array($r) ? (int)Arr::get($r,'n',0) : (int)($r->n ?? 0));
  $label = [
    'rascunho'=>'Rascunho',
    'submetido'=>'Submetido',
    'em_triagem'=>'Em triagem',
    'em_revisao'=>'Em revisão',
    'revisao_solicitada'=>'Correções solicitadas',
    'aceito'=>'Aceito',
    'publicado'=>'Publicado',
    'rejeitado'=>'Rejeitado',
  ];
@endphp

<style>
  .funnel-row{display:flex;align-items:center;gap:.75rem;margin:.35rem 0}
  .funnel-badge{min-width:11rem;font-size:.85rem}
  .funnel-bar{flex:1;height:.8rem;border:1px solid var(--line);background:var(--soft);border-radius:999px;overflow:hidden}
  .funnel-bar > i{display:block;height:100%;background:var(--brand)}
  .muted{color:var(--muted)}
</style>

@if($rows->isEmpty())
  <div class="muted">Sem dados para o período/filtros.</div>
@else
  <div>
    @foreach($rows as $r)
      @php
        $s = is_array($r) ? ($r['status'] ?? '') : ($r->status ?? '');
        $n = is_array($r) ? (int)($r['n'] ?? 0) : (int)($r->n ?? 0);
        $p = $total ? round($n * 100 / $total) : 0;
      @endphp
      <div class="funnel-row">
        <div class="funnel-badge">
          <span class="chip">{{ $label[$s] ?? ucfirst(str_replace('_',' ',$s)) }}</span>
        </div>
        <div class="funnel-bar"><i style="width: {{ $p }}%"></i></div>
        <div class="w-16 text-right tabular-nums">{{ $n }}</div>
        <div class="w-12 text-right tabular-nums muted">{{ $p }}%</div>
      </div>
    @endforeach
    <div class="mt-2 text-xs muted">Total: {{ $total }}</div>
  </div>
@endif

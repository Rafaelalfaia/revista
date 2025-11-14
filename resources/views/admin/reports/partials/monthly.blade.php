@php
  use Illuminate\Support\Arr;
  use Illuminate\Support\Carbon;

  $rows = collect($rows ?? []);


  $maxN = (int) (($rows->max(function($r){
      return (int) (is_array($r) ? Arr::get($r,'n',0) : ($r->n ?? 0));
  })) ?? 0);
@endphp

<style>
  .m-row{display:grid;grid-template-columns:120px 1fr 64px;gap:.5rem;align-items:center;padding:.35rem 0}
  .m-bar{height:.65rem;border:1px solid var(--line);background:var(--soft);border-radius:999px;overflow:hidden}
  .m-bar > i{display:block;height:100%;background:var(--brand)}
  .muted{color:var(--muted)}
</style>

@if($rows->isEmpty())
  <div class="muted">Sem dados mensais para o período.</div>
@else
  <div>
    @foreach($rows as $r)
      @php
        $mRaw  = is_array($r) ? ($r['m'] ?? null) : ($r->m ?? null);
        $n     = (int) (is_array($r) ? ($r['n'] ?? 0) : ($r->n ?? 0));
        $pct   = $maxN > 0 ? (int) round($n * 100 / $maxN) : 0;
        $label = $mRaw ? Carbon::parse($mRaw)->translatedFormat('M/Y') : '—';
      @endphp
      <div class="m-row">
        <div class="muted">{{ $label }}</div>
        <div class="m-bar"><i style="width: {{ $pct }}%"></i></div>
        <div class="text-right tabular-nums">{{ $n }}</div>
      </div>
    @endforeach
  </div>
@endif

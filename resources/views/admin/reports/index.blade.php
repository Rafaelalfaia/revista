@extends('console.layout')
@section('title','Relatórios · Admin')
@section('page.title','Relatórios do Sistema')

@push('head')
<style>
  .kpi{border:1px solid var(--line);background:var(--panel);border-radius:1rem;padding:1rem;color:var(--text)}
  .panel{border:1px solid var(--line);background:var(--panel);color:var(--text)}
  .input{width:100%;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.55rem .75rem}
  .btn{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.5rem .9rem;font-weight:600}
  .btn-brand{background:var(--brand);color:#fff;border-color:transparent}
  .muted{color:var(--muted)}
</style>
@endpush

@section('page.actions')
  <div class="flex gap-2">
    <a href="{{ route('admin.reports.export.csv', request()->query()) }}" class="btn">Exportar CSV</a>
    <a href="{{ route('admin.reports.export.pdf', request()->query()) }}" class="btn btn-brand">Exportar PDF</a>
  </div>
@endsection

@section('content')
  <form method="get" class="grid md:grid-cols-5 gap-3 mb-4">
    <input class="input" type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}">
    <input class="input" type="date" name="to"   value="{{ request('to',   $to->format('Y-m-d')) }}">
    <select class="input" name="category_id">@include('partials.options-categories')</select>
    <select class="input" name="edition_id">@include('partials.options-editions')</select>
    <button class="btn btn-brand">Aplicar</button>
  </form>

  <div class="grid md:grid-cols-5 gap-3">
    <div class="kpi"><div class="muted text-xs">Submissões</div><div class="text-2xl font-bold">{{ $kpis['total'] }}</div></div>
    <div class="kpi"><div class="muted text-xs">Aceites</div><div class="text-2xl font-bold">{{ $kpis['acc'] }}</div></div>
    <div class="kpi"><div class="muted text-xs">Rejeições</div><div class="text-2xl font-bold">{{ $kpis['rej'] }}</div></div>
    <div class="kpi"><div class="muted text-xs">Publicados</div><div class="text-2xl font-bold">{{ $kpis['pub'] }}</div></div>
    <div class="kpi"><div class="muted text-xs">Revisões em aberto</div><div class="text-2xl font-bold">{{ $kpis['revOpen'] }}</div></div>
  </div>

  <div class="grid md:grid-cols-2 gap-3 mt-4">
    <div class="panel border rounded-xl p-4">
      <h3 class="font-semibold mb-2">Funil</h3>
      @include('admin.reports.partials.funnel', ['data'=>$funnel])
    </div>
    <div class="panel border rounded-xl p-4">
      <h3 class="font-semibold mb-2">Submissões por mês</h3>
      @include('admin.reports.partials.monthly', ['rows'=>$monthly])
    </div>
  </div>

  <div class="panel border rounded-xl p-4 mt-4">
    <h3 class="font-semibold mb-2">Revisões em atraso</h3>
    @include('admin.reports.partials.overdue', ['rows'=>$overdue])
  </div>
@endsection

@extends('console.layout')
@section('title','Dashboard do Coordenador')
@section('page.title','Dashboard')

@section('content')
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
    @foreach ([
      ['label'=>'Submissões','value'=>$stats['total']],
      ['label'=>'Em triagem','value'=>$stats['triagem']],
      ['label'=>'Em revisão','value'=>$stats['revisao']],
      ['label'=>'Publicados','value'=>$stats['publicado']],
    ] as $c)
      <div class="rounded-xl border panel p-4" style="border-color:var(--line)">
        <div class="text-sm muted">{{ $c['label'] }}</div>
        <div class="mt-1 text-2xl font-semibold">{{ $c['value'] }}</div>
      </div>
    @endforeach
  </div>

  <div class="mt-4 grid md:grid-cols-2 gap-3">
    <div class="rounded-xl border panel p-4" style="border-color:var(--line)">
      <div class="text-sm muted">Revisores cadastrados</div>
      <div class="mt-1 text-2xl font-semibold">{{ $revisoresCount }}</div>
      <div class="mt-3">
        <a href="{{ route('coordenador.revisores.index') }}"
           class="inline-flex items-center brand text-white rounded-lg px-3 h-9 text-sm">
          Gerenciar revisores
        </a>
      </div>
    </div>

    <div class="rounded-xl border panel p-4" style="border-color:var(--line)">
      <div class="text-sm muted">Ações rápidas</div>
      <div class="mt-2 flex flex-wrap gap-2">
        <a href="{{ route('admin.submissions.index') }}" class="rounded-lg border panel px-3 h-9 inline-flex items-center text-sm" style="border-color:var(--line)">Ver submissões</a>
        <a href="{{ route('admin.reports.index') }}" class="rounded-lg border panel px-3 h-9 inline-flex items-center text-sm" style="border-color:var(--line)">Relatórios</a>
      </div>
    </div>
  </div>
@endsection

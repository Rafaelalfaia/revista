@extends('console.layout-author')

@section('title','Dashboard do Autor · Trivento')
@section('page.title','Dashboard do Autor')

@section('content')
@php
  // Badge por status (cores Tailwind com fallback em dark)
  $badge = function(string $s){
    $map = [
      'rascunho'            => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
      'submetido'           => 'bg-amber-500/10 text-amber-600 dark:text-amber-300',
      'em_triagem'          => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-300',
      'em_revisao'          => 'bg-blue-500/10 text-blue-600 dark:text-blue-300',
      'revisao_solicitada'  => 'bg-rose-500/10 text-rose-600 dark:text-rose-300',
      'aceito'              => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300',
      'rejeitado'           => 'bg-red-500/10 text-red-600 dark:text-red-300',
      'publicado'           => 'bg-emerald-600/15 text-emerald-600 dark:text-emerald-300',
    ];
    return $map[$s] ?? 'bg-slate-500/10 text-slate-600 dark:text-slate-300';
  };
@endphp

{{-- Ações rápidas --}}
<div class="flex items-center justify-between gap-2">
  <h2 class="text-lg font-semibold">Bem-vindo(a)</h2>
  <div class="flex items-center gap-2">
    @if (Route::has('autor.submissions.create'))
      <a href="{{ route('autor.submissions.create') }}"
         class="inline-flex items-center rounded-lg px-3 py-2 text-sm text-white"
         style="background:var(--brand)">+ Nova submissão</a>
    @endif
    @if (Route::has('autor.submissions.index'))
      <a href="{{ route('autor.submissions.index') }}"
         class="inline-flex items-center rounded-lg px-3 py-2 text-sm border panel">Minhas submissões</a>
    @endif
  </div>
</div>

{{-- KPIs --}}
<div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
  <div class="rounded-xl panel border p-4">
    <div class="text-sm muted">Total</div>
    <div class="mt-1 text-2xl font-semibold">{{ $stats['total'] ?? 0 }}</div>
  </div>
  <div class="rounded-xl panel border p-4">
    <div class="text-sm muted">Rascunhos</div>
    <div class="mt-1 text-2xl font-semibold">{{ $stats['rascunho'] ?? 0 }}</div>
  </div>
  <div class="rounded-xl panel border p-4">
    <div class="text-sm muted">Submetidas</div>
    <div class="mt-1 text-2xl font-semibold">{{ $stats['submetido'] ?? 0 }}</div>
  </div>
  <div class="rounded-xl panel border p-4">
    <div class="text-sm muted">Publicadas</div>
    <div class="mt-1 text-2xl font-semibold">{{ $stats['publicado'] ?? 0 }}</div>
  </div>
</div>

{{-- Painéis --}}
<div class="mt-4 grid gap-3 lg:grid-cols-3">
  <div class="rounded-xl panel border p-4">
    <div class="text-sm font-medium">A fazer</div>
    <ul class="mt-3 space-y-2 text-sm">
      @forelse(($rascunhos ?? collect())->take(3) as $s)
        <li class="flex items-center justify-between gap-2">
          <span class="line-clamp-1">{{ $s->title }}</span>
          <a class="hover:underline muted" href="{{ route('autor.submissions.wizard',$s) }}">Continuar</a>
        </li>
      @empty
        <li class="muted">Sem rascunhos pendentes.</li>
      @endforelse

      @foreach(($paraCorrigir ?? collect())->take(3) as $s)
        <li class="flex items-center justify-between gap-2">
          <span class="line-clamp-1">{{ $s->title }}</span>
          <a class="hover:underline muted" href="{{ route('autor.submissions.wizard',$s) }}">Enviar correções</a>
        </li>
      @endforeach
    </ul>
  </div>

  <div class="rounded-xl panel border p-4">
    <div class="text-sm font-medium">Em revisão</div>
    <div class="mt-2 text-3xl font-semibold">{{ $stats['em_revisao'] ?? ($stats['revisao'] ?? 0) }}</div>
    <p class="mt-1 text-sm muted">Aguarde os pareceres dos revisores.</p>
  </div>

  <div class="rounded-xl panel border p-4">
    <div class="text-sm font-medium">Aceitas / Rejeitadas</div>
    <div class="mt-2 flex items-center gap-6">
      <div>
        <div class="text-xs muted">Aceitas</div>
        <div class="text-2xl font-semibold">{{ $stats['aceito'] ?? 0 }}</div>
      </div>
      <div>
        <div class="text-xs muted">Rejeitadas</div>
        <div class="text-2xl font-semibold">{{ $stats['rejeitado'] ?? 0 }}</div>
      </div>
    </div>
    <p class="mt-1 text-sm muted">Decisões finais do editor.</p>
  </div>
</div>

{{-- Últimas submissões --}}
<div class="mt-6 rounded-2xl panel border">
  <div class="p-4 sm:p-5 flex items-center justify-between">
    <div class="font-medium">Últimas submissões</div>
    @if (Route::has('autor.submissions.index'))
      <a href="{{ route('autor.submissions.index') }}" class="text-sm hover:underline muted">ver todas</a>
    @endif
  </div>

  <div class="px-4 sm:px-5 pb-4 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-left muted">
        <tr class="border-b" style="border-color:var(--line)">
          <th class="py-2">Título</th>
          <th class="py-2">Status</th>
          <th class="py-2 hidden sm:table-cell">Criada</th>
          <th class="py-2">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse(($recentes ?? collect()) as $s)
          <tr class="border-b last:border-0" style="border-color:var(--line)">
            <td class="py-2 pr-3">
              <div class="line-clamp-1 font-medium">{{ $s->title }}</div>
            </td>
            <td class="py-2 pr-3">
              <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs {{ $badge($s->status) }}">
                {{ str_replace('_',' ', $s->status) }}
              </span>
            </td>
            <td class="py-2 pr-3 hidden sm:table-cell">
              <span class="muted">{{ $s->created_at?->format('d/m/Y H:i') }}</span>
            </td>
            <td class="py-2">
              @if(in_array($s->status, ['rascunho','revisao_solicitada']))
                <a href="{{ route('autor.submissions.wizard',$s) }}" class="hover:underline muted">Editar</a>
              @else
                <a href="{{ route('autor.submissions.wizard',$s) }}" class="hover:underline muted">Ver</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td class="py-6 muted" colspan="4">Nada por aqui ainda.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

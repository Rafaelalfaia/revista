@extends('console.layout')
@section('title','Submissões')
@section('page.title','Submissões dos meus revisores')

@section('content')
  @if (session('ok'))
    <div class="mb-3 rounded-lg border panel p-3 text-sm" style="border-color:var(--line)">{{ session('ok') }}</div>
  @endif
  @if (session('err'))
    <div class="mb-3 rounded-lg border panel p-3 text-sm text-rose-500" style="border-color:var(--line)">{{ session('err') }}</div>
  @endif

  <form method="GET" class="mb-4 grid sm:grid-cols-[1fr_220px] gap-2">
    <input type="search" name="q" value="{{ $q }}" placeholder="Buscar por título"
           class="rounded-lg border panel h-10 px-3 bg-transparent focus:ring-2 focus:ring-rose-500/60"
           style="border-color:var(--line)">
    <select name="status" class="rounded-lg border panel h-10 px-3 bg-transparent"
            style="border-color:var(--line)">
      <option value="">Todos os status</option>
      @foreach ([
        \App\Models\Submission::ST_DRAFT      => 'Rascunho',
        \App\Models\Submission::ST_SUBMITTED  => 'Submetido',
        \App\Models\Submission::ST_SCREEN     => 'Em triagem',
        \App\Models\Submission::ST_REVIEW     => 'Em revisão',
        \App\Models\Submission::ST_REV_REQ    => 'Revisão solicitada',
        \App\Models\Submission::ST_ACCEPTED   => 'Aceito',
        \App\Models\Submission::ST_PUBLISHED  => 'Publicado',
      ] as $k=>$v)
        <option value="{{ $k }}" {{ $status===$k ? 'selected' : '' }}>{{ $v }}</option>
      @endforeach
    </select>
  </form>

  <div class="overflow-x-auto rounded-2xl border panel" style="border-color:var(--line)">
    <table class="min-w-full text-sm">
      <thead class="panel-2">
        <tr>
          <th class="text-left p-3">Título</th>
          <th class="text-left p-3">Status</th>
          <th class="text-left p-3">Revisor(es)</th>
          <th class="text-center p-3">Correções</th>
          <th class="text-left p-3">Atualizado</th>
          <th class="text-right p-3">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($subs as $s)
          @php
            $correcoes = $s->reviews->where('status','revisao_solicitada')->count();
          @endphp
          <tr class="border-t" style="border-color:var(--line)">
            <td class="p-3">{{ $s->title }}</td>
            <td class="p-3">
              <span class="chip px-2 py-0.5 rounded-md">{{ $s->status }}</span>
            </td>
            <td class="p-3">
              <div class="flex flex-wrap gap-1">
                @foreach ($s->reviews as $rv)
                  <span class="rounded-md border px-2 py-0.5" style="border-color:var(--line)">{{ $rv->reviewer->name }}</span>
                @endforeach
              </div>
            </td>
            <td class="p-3 text-center">{{ $correcoes }}</td>
            <td class="p-3">{{ $s->updated_at?->format('d/m/Y H:i') }}</td>
            <td class="p-3 text-right">
              <a href="{{ route('coordenador.submissions.show', $s) }}"
                 class="inline-flex items-center rounded-lg px-3 h-9 text-sm border panel"
                 style="border-color:var(--line)">Abrir</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="p-6 muted">Nenhuma submissão encontrada para seus revisores.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $subs->links() }}</div>
@endsection

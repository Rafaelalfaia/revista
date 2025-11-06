@extends('console.layout')
@section('title','Triagem · Admin')
@section('page.title','Triagem')

@section('content')
  @if (session('ok'))
    <div class="mb-3 rounded-lg border px-3 py-2 text-sm" style="border-color:var(--line)">
      {{ session('ok') }}
    </div>
  @endif

  {{-- Tabs/buckets --}}
  <div class="mb-3 flex flex-wrap gap-2">
    @php
      $tabs = [
        'submetido'      => 'Submetido',
        'pendente_autor' => 'Correções pendentes',
        'em_triagem'     => 'Em triagem',
      ];
    @endphp
    @foreach($tabs as $key => $label)
      <a href="{{ route('admin.triage.index', array_merge(request()->only(['q','from','to']), ['bucket'=>$key])) }}"
         class="rounded-full px-3 py-1.5 text-sm border"
         style="border-color:var(--line);{{ $bucket===$key ? 'background:var(--chip)' : '' }}">
        {{ $label }}
        @if(isset($counts[$key])) <span class="ml-1 opacity-70">({{ $counts[$key] }})</span>@endif
      </a>
    @endforeach
  </div>

  {{-- Filtros --}}
  <form method="GET" class="rounded-xl panel border p-3 mb-3">
    <input type="hidden" name="bucket" value="{{ $bucket }}">
    <div class="grid sm:grid-cols-4 gap-2">
      <input name="q" value="{{ $q }}" placeholder="Buscar por título/slug"
             class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
      <input type="date" name="from" value="{{ $from }}" class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
      <input type="date" name="to"   value="{{ $to   }}" class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
      <button class="rounded-lg px-3 py-2 text-sm text-white brand">Filtrar</button>
    </div>
  </form>

  {{-- Lista --}}
  <div class="rounded-xl panel border overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-left muted">
        <tr class="border-b" style="border-color:var(--line)">
          <th class="py-2 px-3">ID</th>
          <th class="py-2 px-3">Título</th>
          <th class="py-2 px-3">Autor</th>
          <th class="py-2 px-3">Criada</th>
          <th class="py-2 px-3">Ações rápidas</th>
          <th class="py-2 px-3 text-right">Abrir</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($rows as $s)
          <tr class="border-b last:border-0 align-top" style="border-color:var(--line)">
            <td class="py-2 px-3">#{{ $s->id }}</td>
            <td class="py-2 px-3">
              <div class="font-medium line-clamp-1">{{ $s->title }}</div>
              <div class="text-xs muted">Status: {{ str_replace('_',' ', $s->status) }}</div>
            </td>
            <td class="py-2 px-3">{{ $s->author?->name ?? '—' }}</td>
            <td class="py-2 px-3">{{ $s->created_at?->format('d/m/Y H:i') }}</td>
            <td class="py-2 px-3">
              {{-- Ações rápidas chamam o MESMO endpoint de transition das submissões --}}
              <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.submissions.transition',$s) }}">@csrf
                  <input type="hidden" name="message" value="">
                  <button name="action" value="desk_reject" class="rounded-lg px-2.5 py-1.5 text-xs border" style="border-color:var(--line)">Desk reject</button>
                </form>
                <form method="POST" action="{{ route('admin.submissions.transition',$s) }}">@csrf
                  <input type="hidden" name="message" value="Por favor, ajuste a formatação/anonimização conforme as diretrizes.">
                  <button name="action" value="request_fixes" class="rounded-lg px-2.5 py-1.5 text-xs border" style="border-color:var(--line)">Pedir correções</button>
                </form>
                <form method="POST" action="{{ route('admin.submissions.transition',$s) }}">@csrf
                  <input type="hidden" name="message" value="Encaminhado para revisão por pares.">
                  <button name="action" value="send_to_review" class="rounded-lg px-2.5 py-1.5 text-xs text-white brand">Enviar à revisão</button>
                </form>
              </div>
            </td>
            <td class="py-2 px-3 text-right">
              <a href="{{ route('admin.triage.show',$s) }}" class="hover:underline muted">Abrir</a>
            </td>
          </tr>
        @empty
          <tr><td class="py-6 px-3 muted" colspan="6">Nenhuma submissão neste bucket.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $rows->links() }}</div>
@endsection

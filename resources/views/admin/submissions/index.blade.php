@extends('console.layout')
@section('title','Submissões · Admin')
@section('page.title','Submissões')

@section('content')
  @if (session('ok'))
    <div class="mb-3 rounded-lg border px-3 py-2 text-sm" style="border-color:var(--line)">
      {{ session('ok') }}
    </div>
  @endif

  {{-- Filtros --}}
  <form method="GET" class="rounded-xl panel border p-3 mb-3">
    <div class="grid sm:grid-cols-6 gap-2">
    <input name="q" value="{{ $q }}" placeholder="Buscar por título/slug"
            class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">

    <select name="status" class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
        <option value="">Todos status</option>
        @php $opts = ['rascunho','submetido','em_triagem','em_revisao','revisao_solicitada','aceito','rejeitado','publicado']; @endphp
        @foreach($opts as $opt)
        <option value="{{ $opt }}" @selected($status===$opt)>{{ str_replace('_',' ',$opt) }}</option>
        @endforeach
    </select>

    {{-- Autor --}}
    <select name="author_id" class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
        <option value="">Todos autores</option>
        @foreach($authors as $a)
        <option value="{{ $a->id }}" @selected(($authorId ?? null)===$a->id)>{{ $a->name }}</option>
        @endforeach
    </select>

    {{-- Categoria --}}
    <select name="category_id" class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
        <option value="">Todas categorias</option>
        @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected(($categoryId ?? null)===$c->id)>{{ $c->name }}</option>
        @endforeach
    </select>

    {{-- Campo de data --}}
    <select name="date_field" class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
        @php $df = $dateField ?? 'created_at'; @endphp
        <option value="created_at"   @selected($df==='created_at')>Criada em</option>
        <option value="submitted_at" @selected($df==='submitted_at')>Submetida em</option>
    </select>

    {{-- Período --}}
    <div class="grid grid-cols-2 gap-2">
        <input type="date" name="from" value="{{ $from }}" class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
        <input type="date" name="to"   value="{{ $to   }}" class="rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
    </div>
    </div>

    <div class="mt-2 flex gap-2">
      <button class="rounded-lg px-3 py-2 text-sm text-white brand">Filtrar</button>
      <a href="{{ route('admin.submissions.index') }}" class="text-sm muted hover:underline">limpar</a>
    </div>
  </form>

  {{-- Cards de contagem rápida --}}
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
    @foreach (['submetido','em_triagem','em_revisao','revisao_solicitada'] as $k)
      <div class="rounded-xl panel border p-4">
        <div class="text-sm muted">{{ str_replace('_',' ',$k) }}</div>
        <div class="mt-1 text-2xl font-semibold">{{ $stats[$k] ?? 0 }}</div>
      </div>
    @endforeach
  </div>

  {{-- Tabela --}}
  <div class="rounded-xl panel border overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-left muted">
        <tr class="border-b" style="border-color:var(--line)">
          <th class="py-2 px-3">ID</th>
          <th class="py-2 px-3">Título</th>
          <th class="py-2 px-3">Autor</th>
          <th class="py-2 px-3">Status</th>
          <th class="py-2 px-3">Criada</th>
          <th class="py-2 px-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($rows as $s)
          <tr class="border-b last:border-0" style="border-color:var(--line)">
            <td class="py-2 px-3">#{{ $s->id }}</td>
            <td class="py-2 px-3"><div class="line-clamp-1 font-medium">{{ $s->title }}</div></td>
            <td class="py-2 px-3">{{ $s->author?->name ?? '—' }}</td>
            <td class="py-2 px-3">
              <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs chip">
                {{ str_replace('_',' ',$s->status) }}
              </span>
            </td>
            <td class="py-2 px-3">{{ $s->created_at?->format('d/m/Y H:i') }}</td>
            <td class="py-2 px-3 text-right">
              <a href="{{ route('admin.submissions.show',$s) }}" class="hover:underline muted">Abrir</a>
            </td>
          </tr>
        @empty
          <tr><td class="py-6 px-3 muted" colspan="6">Nenhum registro.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">{{ $rows->links() }}</div>
@endsection

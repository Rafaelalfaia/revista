@extends('console.layout')
@section('title','Revisores')
@section('page.title','Revisores')

@section('content')
  @if (session('ok'))
    <div class="mb-3 rounded-lg border panel p-3 text-sm" style="border-color:var(--line)">{{ session('ok') }}</div>
  @endif
  @if (session('err'))
    <div class="mb-3 rounded-lg border panel p-3 text-sm text-rose-500" style="border-color:var(--line)">{{ session('err') }}</div>
  @endif

  <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
    <form method="GET" class="flex-1">
      <input type="search" name="q" value="{{ $q }}" placeholder="Buscar por nome, e-mail ou CPF"
             class="w-full rounded-lg border panel h-10 px-3 bg-transparent focus:ring-2 focus:ring-rose-500/60"
             style="border-color:var(--line)">
    </form>
    @can('reviewers.manage')
      <a href="{{ route('coordenador.revisores.create') }}"
         class="brand text-white rounded-lg px-4 h-10 inline-flex items-center justify-center text-sm">+ Novo revisor</a>
    @endcan
  </div>

  <div class="overflow-x-auto rounded-2xl border panel" style="border-color:var(--line)">
    <table class="min-w-full text-sm">
      <thead class="panel-2">
        <thead class="panel-2">
        <tr>
            <th class="text-left p-3">Nome</th>
            <th class="text-left p-3">E-mail</th>
            <th class="text-left p-3">CPF</th>
            <th class="text-left p-3">Áreas</th>
            <th class="text-right p-3">Ações</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($revisores as $u)
            <tr class="border-t" style="border-color:var(--line)">
            <td class="p-3">{{ $u->name }}</td>
            <td class="p-3 muted">{{ $u->email ?: '—' }}</td>
            <td class="p-3">{{ $u->cpf_formatted ?? $u->cpf ?? '—' }}</td>
            <td class="p-3">
                <div class="flex flex-wrap gap-1">
                @forelse ($u->categories as $cat)
                    <span class="chip rounded-md px-2 py-0.5 text-xs">{{ $cat->name }}</span>
                @empty
                    <span class="muted text-xs">—</span>
                @endforelse
                </div>
            </td>
            <td class="p-3 text-right whitespace-nowrap">
                {{-- ações iguais --}}
            </td>
            </tr>
        @empty
            <tr><td class="p-6 muted" colspan="5">Nenhum revisor encontrado.</td></tr>
        @endforelse
        </tbody>

    </table>
  </div>

  <div class="mt-4">{{ $revisores->links() }}</div>
@endsection

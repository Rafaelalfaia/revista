@extends('console.layout')
@section('title','Pessoas')
@section('page.title','Pessoas')

@section('content')
  @if (session('ok'))
    <div class="mb-3 rounded-lg border panel p-3 text-sm" style="border-color:var(--line)">{{ session('ok') }}</div>
  @endif
  @if (session('err'))
    <div class="mb-3 rounded-lg border panel p-3 text-sm text-rose-500" style="border-color:var(--line)">{{ session('err') }}</div>
  @endif

  <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
    <form method="GET" class="flex-1">
      <input type="search" name="q" value="{{ $q }}" placeholder="Buscar por nome ou e-mail"
             class="w-full rounded-lg border px-3 h-10 panel" style="border-color:var(--line)">
    </form>

    @can('users.manage')
      <a href="{{ route('admin.users.create') }}"
         class="brand text-white rounded-lg px-4 h-10 inline-flex items-center justify-center text-sm">+ Novo</a>
    @endcan
  </div>

  <div class="overflow-x-auto rounded-2xl border panel" style="border-color:var(--line)">
    <table class="min-w-full text-sm">
      <thead class="panel-2">
        <tr>
          <th class="text-left p-3">Nome</th>
          <th class="text-left p-3">E-mail</th>
          <th class="text-left p-3">Papel</th>
          <th class="text-right p-3">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $u)
          <tr class="border-t" style="border-color:var(--line)">
            <td class="p-3">{{ $u->name }}</td>
            <td class="p-3 muted">{{ $u->email }}</td>
            <td class="p-3">
              <span class="chip px-2 py-1 rounded-md text-xs">
                {{ $u->roles->pluck('name')->first() ?? '—' }}
              </span>
            </td>
            <td class="p-3 text-right whitespace-nowrap">
              @can('users.manage')
                <a href="{{ route('admin.users.edit',$u) }}"
                   class="inline-flex items-center rounded-lg px-3 h-9 text-sm border panel"
                   style="border-color:var(--line)">Editar</a>

                <form action="{{ route('admin.users.destroy',$u) }}" method="POST" class="inline"
                      onsubmit="return confirm('Excluir este usuário?');">
                  @csrf @method('DELETE')
                  <button class="inline-flex items-center rounded-lg px-3 h-9 text-sm border panel ml-2 text-rose-600"
                          style="border-color:var(--line)">Excluir</button>
                </form>
              @endcan
            </td>
          </tr>
        @empty
          <tr><td class="p-6 muted" colspan="4">Nenhuma pessoa encontrada.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $users->links() }}</div>
@endsection

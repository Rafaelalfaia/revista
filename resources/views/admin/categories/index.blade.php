@extends('console.layout')
@section('title','Categorias · Admin')
@section('page.title','Categorias')

@section('content')
  @if (session('ok'))
    <div class="mb-3 rounded-lg border px-3 py-2 text-sm" style="border-color:var(--line)">
      {{ session('ok') }}
    </div>
  @endif

  <div class="grid lg:grid-cols-3 gap-4">

    <div class="lg:col-span-2 rounded-xl panel border p-4">
      <div class="flex items-center justify-between gap-3 mb-3">
        <form method="GET" class="flex items-center gap-2">
          <input name="q" value="{{ $q }}" placeholder="Buscar..." class="rounded-lg border px-3 py-2 text-sm bg-transparent"
                 style="border-color:var(--line)">
          <button class="rounded-lg px-3 py-2 text-sm text-white brand">Buscar</button>
        </form>
        <a href="{{ route('admin.categories.index') }}" class="text-sm muted hover:underline">limpar</a>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="text-left muted">
            <tr class="border-b" style="border-color:var(--line)">
              <th class="py-2">Ícone</th>
              <th class="py-2">Nome</th>
              <th class="py-2">Slug</th>
              <th class="py-2">Ativa</th>
              <th class="py-2">Ordem</th>
              <th class="py-2"></th>
            </tr>
          </thead>
          <tbody>
          @forelse($cats as $c)
            <tr class="border-b last:border-0" style="border-color:var(--line)">
              <td class="py-2 pr-3">
                @if($c->icon_url)
                  <img src="{{ $c->icon_url }}" class="h-8 w-8 object-contain rounded" alt="">
                @else
                  <span class="muted">—</span>
                @endif
              </td>
              <td class="py-2 pr-3 font-medium">{{ $c->name }}</td>
              <td class="py-2 pr-3 muted">{{ $c->slug }}</td>
              <td class="py-2 pr-3">
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $c->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300' : 'bg-slate-500/10 muted' }}">
                  {{ $c->is_active ? 'sim' : 'não' }}
                </span>
              </td>
              <td class="py-2 pr-3">{{ $c->sort_order }}</td>
              <td class="py-2 text-right">
                <a href="{{ route('admin.categories.edit',$c) }}" class="hover:underline muted">Editar</a>
                <form method="POST" action="{{ route('admin.categories.destroy',$c) }}" class="inline"
                      onsubmit="return confirm('Remover categoria?')">
                  @csrf @method('DELETE')
                  <button class="ml-3 hover:underline text-rose-600">Excluir</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-6 muted">Nenhuma categoria.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">{{ $cats->links() }}</div>
    </div>


    <div class="rounded-xl panel border p-4">
      <div class="font-medium mb-2">Nova categoria</div>
      <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div>
          <label class="text-sm">Nome</label>
          <input name="name" class="w-full rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)" required>
          @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm">Slug (opcional)</label>
          <input name="slug" class="w-full rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
          @error('slug') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm">Ícone (PNG/JPEG – opcional)</label>
          <input type="file" name="icon" accept=".png,.jpg,.jpeg" class="block text-sm">
          @error('icon') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="flex gap-3">
          <div class="flex-1">
            <label class="text-sm">Ordem</label>
            <input type="number" name="sort_order" min="0" value="0"
                   class="w-full rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
          </div>
          <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm mt-6">
              <input type="checkbox" name="is_active" value="1" checked class="rounded">
              Ativa
            </label>
          </div>
        </div>

        <div class="pt-2">
          <button class="rounded-lg px-3 py-2 text-sm text-white brand">Criar</button>
        </div>
      </form>
    </div>
  </div>
@endsection

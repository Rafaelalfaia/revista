@extends('console.layout')
@section('title','Editar Categoria · Admin')
@section('page.title','Editar Categoria')

@section('content')
  <a href="{{ route('admin.categories.index') }}" class="text-sm hover:underline muted">← voltar</a>

  <div class="mt-3 grid lg:grid-cols-3 gap-4">

    <div class="rounded-xl panel border p-4 lg:col-span-2">
      <form method="POST" action="{{ route('admin.categories.update',$category) }}" enctype="multipart/form-data" class="space-y-3">
        @csrf @method('PUT')
        <div>
          <label class="text-sm">Nome</label>
          <input name="name" value="{{ old('name',$category->name) }}" required
                 class="w-full rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
          @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm">Slug</label>
          <input name="slug" value="{{ old('slug',$category->slug) }}"
                 class="w-full rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
          @error('slug') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="grid sm:grid-cols-2 gap-3">
          <div>
            <label class="text-sm">Ícone (PNG/JPEG – opcional)</label>
            <input type="file" name="icon" accept=".png,.jpg,.jpeg" class="block text-sm">
            @error('icon') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
          </div>
          <div>
            <label class="text-sm">Ordem</label>
            <input type="number" name="sort_order" min="0" value="{{ old('sort_order',$category->sort_order) }}"
                   class="w-full rounded-lg border px-3 py-2 text-sm bg-transparent" style="border-color:var(--line)">
            @error('sort_order') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
          </div>
        </div>
        <div>
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active',$category->is_active) ? 'checked' : '' }} class="rounded">
            Ativa
          </label>
          @error('is_active') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="pt-2 flex gap-2">
          <button class="rounded-lg px-3 py-2 text-sm text-white brand">Salvar</button>
          <a href="{{ route('admin.categories.index') }}" class="rounded-lg px-3 py-2 text-sm border panel">Cancelar</a>
        </div>
      </form>
    </div>

    <div class="rounded-xl panel border p-4">
      <div class="text-sm font-medium mb-2">Pré-visualização</div>
      @if ($category->icon_url)
        <img src="{{ $category->icon_url }}" class="h-20 w-20 object-contain rounded" alt="">
      @else
        <div class="muted text-sm">Sem ícone.</div>
      @endif
    </div>
  </div>
@endsection

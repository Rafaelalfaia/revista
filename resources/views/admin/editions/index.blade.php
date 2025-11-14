@extends('console.layout')
@section('title','Edições')
@section('page.title','Edições')

@section('page.actions')
  <a href="{{ route('admin.editions.create') }}" class="px-3 py-2 rounded-lg bg-[var(--brand)] text-white font-semibold">➕ Nova edição</a>
@endsection

@push('head')
<style>
  .card{border:1px solid var(--line);background:var(--panel);border-radius:1rem;transition:.18s ease;box-shadow:0 1px 0 rgba(0,0,0,.02)}
  .card:hover{transform:translateY(-1px);box-shadow:0 6px 14px rgba(0,0,0,.06)}
  .thumb{width:64px;height:64px;border-radius:.75rem;overflow:hidden;border:1px solid var(--line);background:var(--soft)}
  .title{font-weight:700;line-height:1.2}
  .meta{font-size:.8rem;color:var(--muted)}
  .input{width:100%;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.6rem .8rem}
  .btn{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.45rem .8rem;font-weight:600;white-space:nowrap}
  .btn-sm{padding:.38rem .7rem;font-size:.9rem}
  .btn-brand{background:var(--brand);color:#fff;border-color:transparent}
  .btn-ghost{background:transparent}
  .muted{color:var(--muted)}
  .actions{display:flex;gap:.5rem;flex-wrap:wrap}
</style>
@endpush

@section('content')
  @if(session('ok')) <div class="mb-4 text-sm rounded-md p-3 bg-emerald-600/10 border border-emerald-600/30 text-emerald-600">{{ session('ok') }}</div> @endif
  @if(session('error')) <div class="mb-4 text-sm rounded-md p-3 bg-rose-600/10 border border-rose-600/30 text-rose-600">{{ session('error') }}</div> @endif

  <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <input class="input" type="text" name="q" value="{{ $q ?? request('q') }}" placeholder="Buscar edição">
    <div class="md:col-span-2"></div>
  </form>

  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @forelse($editions as $ed)
      @php $count = method_exists($ed,'submissions') ? $ed->submissions()->count() : 0; @endphp
      <div class="card overflow-hidden">
        <div class="flex gap-3 p-4">
          <div class="thumb">
            @if($ed->cover_photo_path)
              <img class="w-full h-full object-cover" src="{{ Storage::disk($ed->cover_photo_disk ?? 'public')->url($ed->cover_photo_path) }}" alt="">
            @else
              <div class="w-full h-full grid place-items-center text-xs muted">sem capa</div>
            @endif
          </div>
          <div class="min-w-0 flex-1">
            <div class="title truncate">{{ $ed->title }}</div>
            <div class="meta mt-1">
              Lançamento: {{ optional($ed->release_date)->format('d/m/Y') ?? '—' }} · Publicações: {{ $count }}
            </div>
          </div>
        </div>

        <div class="border-t border-[var(--line)] px-4 py-3">
          <div class="actions">
            <a href="{{ route('admin.editions.edit',$ed) }}" class="btn btn-sm">Editar</a>
            <a href="{{ route('admin.editions.submissions.index',$ed) }}" class="btn btn-sm btn-brand">Gerenciar publicações</a>
            <form method="POST" action="{{ route('admin.editions.destroy',$ed) }}" onsubmit="return confirm('Remover esta edição?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-ghost">Excluir</button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="muted">Nenhuma edição encontrada.</div>
    @endforelse
  </div>

  <div class="mt-6">{{ $editions->links() }}</div>
@endsection

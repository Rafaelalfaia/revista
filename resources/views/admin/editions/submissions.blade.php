@extends('console.layout')
@section('title','Publicações da edição')
@section('page.title','Publicações da edição')

@push('head')
<style>
  .panel{border:1px solid var(--line);background:var(--panel);border-radius:1rem}
  .input{width:100%;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.55rem .75rem}
  .btn{border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.5rem .9rem;font-weight:600}
  .btn-brand{background:var(--brand);color:#fff;border-color:transparent}
  .muted{color:var(--muted)}
  .row{display:grid;grid-template-columns:1fr auto;gap:.5rem;align-items:center}
  .handle{cursor:grab}
</style>
@endpush

@section('page.actions')
  <a href="{{ route('admin.editions.edit',$edition) }}" class="px-3 py-2 rounded-lg bg-[var(--brand)] text-white font-semibold">Voltar à edição</a>
@endsection

@section('content')
  @if(session('ok')) <div class="mb-4 text-sm rounded-md p-3 bg-emerald-600/10 border border-emerald-600/30 text-emerald-600">{{ session('ok') }}</div> @endif
  @if(session('warn')) <div class="mb-4 text-sm rounded-md p-3 bg-amber-600/10 border border-amber-600/30 text-amber-700">{{ session('warn') }}</div> @endif
  @if(session('error')) <div class="mb-4 text-sm rounded-md p-3 bg-rose-600/10 border border-rose-600/30 text-rose-600">{{ session('error') }}</div> @endif

  <div class="grid gap-4 lg:grid-cols-2">
    <div class="panel p-4" x-data="sortable(@js($current->pluck('id')))">
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold">Na edição ({{ $current->count() }})</div>
        <form x-ref="reorderForm" method="POST" action="{{ route('admin.editions.submissions.reorder',$edition) }}">
          @csrf @method('PATCH')
          <template x-for="id in order" hidden>
            <input type="hidden" name="order[]" :value="id">
          </template>
          <button type="submit" class="btn">Salvar ordem</button>
        </form>
      </div>

      <div class="grid gap-2" x-ref="list">
        @forelse($current as $item)
          @php
            $showUrl = \Illuminate\Support\Facades\Route::has('admin.submissions.show')
                ? route('admin.submissions.show', $item)
                : (\Illuminate\Support\Facades\Route::has('admin.submissions.read')
                    ? route('admin.submissions.read', $item)
                    : null);
            $author = optional($item->user)->name;
            $names = $reviewersBySubmission[$item->id] ?? collect();
            $reviewersList = $names instanceof \Illuminate\Support\Collection ? $names->join(', ') : (is_array($names) ? implode(', ', $names) : (string) $names);
         @endphp
            <div class="row p-3 rounded-lg border border-[var(--line)] bg-[var(--soft)]" draggable="true" data-id="{{ $item->id }}">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                <svg class="handle muted" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M8 6h2v2H8V6m6 0h2v2h-2V6M8 11h2v2H8v-2m6 0h2v2h-2v-2M8 16h2v2H8v-2m6 0h2v2h-2v-2"/></svg>
                <div class="text-sm font-semibold truncate">{{ $item->title }}</div>
                </div>
                <div class="text-xs muted truncate mt-1">
                Autor: {{ $author ?? '—' }} · Revisor: {{ $reviewersList ?: '—' }}
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($showUrl)
                <a href="{{ $showUrl }}" target="_blank" class="btn">Ver</a>
                @endif
                <form method="POST" action="{{ route('admin.editions.submissions.destroy',[$edition,$item]) }}" onsubmit="return confirm('Remover da edição?')">
                @csrf @method('DELETE')
                <button class="btn">Remover</button>
                </form>
            </div>
            </div>

        @empty
          <div class="muted">Nenhuma publicação nesta edição.</div>
        @endforelse
      </div>
    </div>

    <div class="panel p-4">
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold">Adicionar publicações aprovadas</div>
        <div class="text-xs muted">Encontradas: {{ $eligible->count() }}</div>
      </div>

      <form method="GET" class="mb-3">
        <input class="input" type="text" name="q" value="{{ $q }}" placeholder="Buscar por título ou slug">
      </form>

      <div class="grid gap-2">
        @forelse($eligible as $sub)
          @php
            $names = $reviewersBySubmission[$sub->id] ?? collect();
            $reviewersList = $names instanceof \Illuminate\Support\Collection ? $names->join(', ') : (is_array($names) ? implode(', ', $names) : (string) $names);
            $showUrl = \Illuminate\Support\Facades\Route::has('admin.submissions.show')
                ? route('admin.submissions.show', $sub)
                : (\Illuminate\Support\Facades\Route::has('admin.submissions.read')
                    ? route('admin.submissions.read', $sub)
                    : null);
            @endphp
            <form method="POST" action="{{ route('admin.editions.submissions.store',$edition) }}" class="row p-3 rounded-lg border border-[var(--line)]">
            @csrf
            <div class="min-w-0">
                <div class="text-sm font-semibold truncate">{{ $sub->title }}</div>
                <div class="text-xs muted truncate mt-1">
                Autor: {{ optional($sub->user)->name ?? '—' }} · Revisor: {{ $reviewersList ?: '—' }}
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($showUrl)
                <a href="{{ $showUrl }}" target="_blank" class="btn">Ver</a>
                @endif
                <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                <button class="btn btn-brand">Adicionar</button>
            </div>
            </form>

        @empty
          <div class="muted">Nenhuma elegível encontrada.</div>
        @endforelse
      </div>
    </div>
  </div>

  <script>
    function sortable(initial){
      return {
        order: initial || [],
        init(){
          const list = this.$refs.list;
          const rows = () => Array.from(list.querySelectorAll('[draggable=true]'));
          let dragEl = null;
          rows().forEach(el => {
            el.addEventListener('dragstart', e => { dragEl = el; e.dataTransfer.effectAllowed='move'; el.classList.add('opacity-60'); });
            el.addEventListener('dragend',   e => { el.classList.remove('opacity-60'); dragEl=null; this.refresh(); });
            el.addEventListener('dragover',  e => { e.preventDefault(); const cur = e.currentTarget; if(!dragEl || cur===dragEl) return; const rect=cur.getBoundingClientRect(); const after=(e.clientY - rect.top) > rect.height/2; cur.parentNode.insertBefore(dragEl, after? cur.nextSibling : cur); });
          });
          this.refresh();
        },
        refresh(){
          this.order = Array.from(this.$refs.list.children).map(el => parseInt(el.getAttribute('data-id')));
        }
      }
    }
  </script>
@endsection

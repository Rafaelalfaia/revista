@extends('console.layout-author')
@section('title','Seções do Trabalho')
@section('page.title','Estrutura (sumário automático)')

@section('content')
  @if(session('ok'))
    <div class="mb-4 rounded panel border px-4 py-2" style="border-left:4px solid var(--brand)">{{ session('ok') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-4 rounded panel border px-4 py-2" style="border-left:4px solid #b91c1c">
      <ul class="list-disc pl-6">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @php $canEdit = $submission->canEditContent(); @endphp

  @if($canEdit)
  <form method="POST" action="{{ route('autor.sections.store',$submission) }}"
        class="mb-6 grid gap-3 rounded-lg panel border p-4">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
      <div class="md:col-span-5">
        <label class="block text-sm font-medium mb-1">Título da seção</label>
        <input name="title" class="w-full rounded border panel p-2 bg-transparent" required>
      </div>
      <div class="md:col-span-4">
        <label class="block text-sm font-medium mb-1">Seção pai (opcional)</label>
        @php
          $all = \DB::table('submission_sections')->where('submission_id',$submission->id)->orderBy('numbering')->get();
        @endphp
        <select name="parent_id" class="w-full rounded border panel p-2 bg-transparent">
          <option value="">— Raiz —</option>
          @foreach($all as $s)
            <option value="{{ $s->id }}">{{ $s->numbering ? $s->numbering.' ' : ''}}{{ str_repeat('— ', max(0,$s->level-1)) }}{{ $s->title }}</option>
          @endforeach
        </select>
      </div>
      <div class="md:col-span-3">
        <label class="block text-sm font-medium mb-1">Posição</label>
        <input name="position" type="number" min="1" class="w-full rounded border panel p-2 bg-transparent">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Conteúdo (HTML/Markdown)</label>
      <textarea name="content" rows="4" class="w-full rounded border panel p-2 bg-transparent"></textarea>
    </div>

    <div class="flex gap-2">
      <a class="rounded border px-4 py-2 panel-2" href="{{ route('autor.submissions.edit',$submission) }}">Voltar</a>
      <button class="rounded px-4 py-2 text-white brand">Adicionar seção</button>
    </div>
  </form>
  @else
    <div class="mb-6 rounded panel border px-4 py-2">Edição de seções bloqueada no status <strong>{{ $submission->status }}</strong>.</div>
  @endif

  @php
    $sections = \DB::table('submission_sections')
      ->where('submission_id',$submission->id)
      ->orderByRaw("COALESCE(numbering,'') ASC")
      ->get();
  @endphp

  <div x-data="reorderTable()" class="overflow-x-auto rounded-lg panel border">
    <table class="min-w-full text-sm">
      <thead class="text-left panel-2">
        <tr>
          <th class="px-4 py-2">#</th>
          <th class="px-4 py-2">Seção</th>
          <th class="px-4 py-2">Pai</th>
          <th class="px-4 py-2">Posição</th>
          <th class="px-4 py-2">TOC</th>
          <th class="px-4 py-2 text-right">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sections as $s)
          <tr class="border-t" style="border-color:var(--line)" x-data="{open:false}">
            <td class="px-4 py-2 font-mono text-xs muted">{{ $s->numbering }}</td>
            <td class="px-4 py-2">
              <div class="flex items-center gap-2">
                @if($canEdit)
                  <button @click="open=!open" class="rounded border px-2 py-1 text-xs panel-2">editar</button>
                @endif
                <span style="margin-left: {{ max(0, ($s->level-1)*16) }}px">{{ $s->title }}</span>
              </div>

              @if($canEdit)
              <div x-show="open" class="mt-2 rounded border panel p-3">
                <form method="POST" action="{{ route('autor.sections.update',[$submission,$s->id]) }}">
                  @csrf @method('PATCH')
                  <div class="grid gap-2">
                    <input name="title" class="w-full rounded border panel p-2 bg-transparent" value="{{ $s->title }}">
                    <textarea name="content" rows="4" class="w-full rounded border panel p-2 bg-transparent">{{ $s->content }}</textarea>
                    <label class="inline-flex items-center gap-2 text-sm">
                      <input type="checkbox" name="show_in_toc" value="1" @checked($s->show_in_toc)>
                      <span class="muted">Mostrar no sumário</span>
                    </label>
                    <div class="flex gap-2">
                      <button class="rounded px-3 py-1 text-white brand">Salvar</button>
                      <button type="button" @click="open=false" class="rounded border px-3 py-1 panel-2">Fechar</button>
                    </div>
                  </div>
                </form>
              </div>
              @endif
            </td>

            <td class="px-4 py-2">
              <select class="rounded border panel p-1 bg-transparent"
                      x-data x-init="$el.value='{{ $s->parent_id }}'"
                      data-id="{{ $s->id }}" data-field="parent_id" @disabled(!$canEdit)>
                <option value="">— Raiz —</option>
                @foreach($sections as $p)
                  <option value="{{ $p->id }}">{{ $p->numbering ? $p->numbering.' ' : ''}}{{ $p->title }}</option>
                @endforeach
              </select>
            </td>
            <td class="px-4 py-2">
              <input type="number" min="1" class="w-24 rounded border panel p-1 bg-transparent"
                     value="{{ $s->position }}" data-id="{{ $s->id }}" data-field="position" @disabled(!$canEdit)>
            </td>

            <td class="px-4 py-2">
              @if($s->show_in_toc)
                <span class="chip rounded px-2 py-1">Sim</span>
              @else
                <span class="muted">Não</span>
              @endif
            </td>

            <td class="px-4 py-2">
              <div class="flex justify-end">
                @if($canEdit)
                <form method="POST" action="{{ route('autor.sections.destroy',[$submission,$s->id]) }}"
                      onsubmit="return confirm('Remover esta seção (e possíveis subseções)?')">
                  @csrf @method('DELETE')
                  <button class="rounded px-3 py-1 text-white" style="background:#b91c1c">Excluir</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-4 py-6 text-center muted">Sem seções ainda.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="flex items-center justify-between p-3">
      <a class="rounded border px-4 py-2 panel-2" href="{{ route('autor.submissions.edit',$submission) }}">Voltar</a>
      @if($canEdit)
        <button class="rounded px-4 py-2 text-white brand"
                @click="save('{{ route('autor.sections.reorder',$submission) }}','{{ csrf_token() }}')">
          Salvar ordenação
        </button>
      @endif
    </div>
  </div>

  <div class="mt-6 rounded-lg panel border p-4">
    <h3 class="mb-2 font-semibold">Sumário (automático)</h3>
    <ul class="text-sm">
      @foreach($sections->where('show_in_toc',1) as $s)
        <li class="py-0.5" style="margin-left: {{ max(0, ($s->level-1)*16) }}px">
          {{ $s->numbering }} {{ $s->title }}
        </li>
      @endforeach
    </ul>
  </div>

  @push('scripts')
  <script>
    function reorderTable(){
      return {
        save(url,token){
          const rows = document.querySelectorAll('tbody tr');
          const items = [];
          rows.forEach(tr=>{
            const pos = tr.querySelector('[data-id][data-field="position"]');
            if(!pos) return;
            const id = pos.dataset.id;
            const position = Number(pos.value || 1);
            const parentSel = tr.querySelector('select[data-field="parent_id"]');
            const parent_id = parentSel && parentSel.value ? Number(parentSel.value) : null;
            items.push({id: Number(id), parent_id, position});
          });
          fetch(url, {
            method:'POST',
            headers:{'X-CSRF-TOKEN':token,'Content-Type':'application/json'},
            body: JSON.stringify({items})
          }).then(r => r.ok ? location.reload() : alert('Falha ao salvar ordem'));
        }
      }
    }
  </script>
  @endpush
@endsection

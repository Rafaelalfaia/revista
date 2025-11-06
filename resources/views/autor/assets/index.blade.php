@extends('console.layout-author')
@section('title','Arquivos da Submissão')
@section('page.title','Arquivos (manuscrito, figuras, tabelas, anexos)')

@section('content')
  @if(session('ok'))
    <div class="mb-4 rounded panel border px-4 py-2" style="border-left:4px solid var(--brand)">{{ session('ok') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-4 rounded panel border px-4 py-2" style="border-left:4px solid #b91c1c">
      <ul class="list-disc pl-6">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @php
    $sections = \DB::table('submission_sections')
      ->where('submission_id',$submission->id)
      ->orderBy('numbering')->get();
    $canEdit = $submission->canEditContent();
  @endphp

  @if($canEdit)
  <form method="POST" action="{{ route('autor.assets.store',$submission) }}" enctype="multipart/form-data"
        class="mb-6 grid gap-3 rounded-lg panel border p-4">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">Tipo</label>
        <select name="type" class="w-full rounded border panel p-2 bg-transparent">
          <option value="manuscript">Manuscrito</option>
          <option value="figure">Figura</option>
          <option value="table">Tabela</option>
          <option value="attachment">Anexo</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Seção (opcional)</label>
        <select name="section_id" class="w-full rounded border panel p-2 bg-transparent">
          <option value="">— sem seção —</option>
          @foreach($sections as $s)
            <option value="{{ $s->id }}">{{ $s->numbering ? $s->numbering.' ' : ''}}{{ str_repeat('— ', max(0,$s->level-1)) }}{{ $s->title }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Arquivo</label>
        <input type="file" name="file" required class="w-full rounded border panel p-2 bg-transparent">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="block text-sm font-medium mb-1">Legenda (caption)</label>
        <input name="caption" class="w-full rounded border panel p-2 bg-transparent">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Fonte (source)</label>
        <input name="source" class="w-full rounded border panel p-2 bg-transparent" placeholder="Ex.: Autor; IBGE, 2022; DOI...">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Ordem</label>
        <input type="number" name="order" min="1" class="w-full rounded border panel p-2 bg-transparent">
      </div>
    </div>

    <div class="flex gap-2">
      <a class="rounded border px-4 py-2 panel-2" href="{{ route('autor.submissions.edit',$submission) }}">Voltar</a>
      <button class="rounded px-4 py-2 text-white brand">Enviar arquivo</button>
    </div>
  </form>
  @else
    <div class="mb-6 rounded panel border px-4 py-2">Edição de arquivos bloqueada no status <strong>{{ $submission->status }}</strong>.</div>
  @endif

  @php
    $assets = \DB::table('submission_assets')
      ->where('submission_id',$submission->id)
      ->orderBy('type')->orderBy('section_id')->orderBy('order')->get()
      ->groupBy('type');
  @endphp

  @forelse($assets as $type=>$list)
    <div class="mb-6 overflow-x-auto rounded-lg panel border">
      <div class="border-b panel-2 px-4 py-2 font-semibold uppercase" style="border-color:var(--line)">{{ $type }}</div>
      <table class="min-w-full text-sm">
        <thead class="text-left panel-2">
          <tr>
            <th class="px-4 py-2">Prévia</th>
            <th class="px-4 py-2">Info</th>
            <th class="px-4 py-2">Seção</th>
            <th class="px-4 py-2">Ordem</th>
            <th class="px-4 py-2 text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach($list as $a)
            <tr class="border-t" style="border-color:var(--line)">
              <td class="px-4 py-2">
                @php $url = Str::startsWith($a->file_path,['http://','https://']) ? $a->file_path : Storage::disk('public')->url($a->file_path); @endphp
                @if(Str::endsWith(Str::lower($a->file_path), ['.png','.jpg','.jpeg','.gif','.webp']))
                  <img src="{{ $url }}" class="h-20 w-auto rounded border object-cover" style="border-color:var(--line)" alt="">
                @else
                  <a href="{{ $url }}" target="_blank" class="underline">abrir</a>
                @endif
              </td>
              <td class="px-4 py-2">
                <div class="text-sm"><strong>Caption:</strong> {{ $a->caption ?? '—' }}</div>
                <div class="text-xs muted"><strong>Fonte:</strong> {{ $a->source ?? '—' }}</div>
              </td>
              <td class="px-4 py-2">
                @php $sec = $a->section_id ? $sections->firstWhere('id',$a->section_id) : null; @endphp
                {{ $sec ? (($sec->numbering ? $sec->numbering.' ' : '').$sec->title) : '—' }}
              </td>
              <td class="px-4 py-2">{{ $a->order }}</td>
              <td class="px-4 py-2">
                <div x-data="{open:false}" class="flex justify-end gap-2">
                  @if($canEdit)<button @click="open=!open" class="rounded border px-3 py-1 panel-2">Editar</button>@endif
                  @if($canEdit)
                    <form method="POST" action="{{ route('autor.assets.destroy',[$submission,$a->id]) }}"
                          onsubmit="return confirm('Remover arquivo?')">
                      @csrf @method('DELETE')
                      <button class="rounded px-3 py-1 text-white" style="background:#b91c1c">Excluir</button>
                    </form>
                  @endif
                </div>

                @if($canEdit)
                <div x-show="open" class="mt-2 rounded border panel p-3">
                  <form method="POST" action="{{ route('autor.assets.update',[$submission,$a->id]) }}">
                    @csrf @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                      <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Seção</label>
                        <select name="section_id" class="w-full rounded border panel p-2 bg-transparent">
                          <option value="">— sem seção —</option>
                          @foreach($sections as $s)
                            <option value="{{ $s->id }}" @selected($a->section_id==$s->id)">
                              {{ $s->numbering ? $s->numbering.' ' : ''}}{{ str_repeat('— ', max(0,$s->level-1)) }}{{ $s->title }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div>
                        <label class="block text-sm font-medium mb-1">Ordem</label>
                        <input type="number" name="order" min="1" value="{{ $a->order }}" class="w-full rounded border panel p-2 bg-transparent">
                      </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                      <div>
                        <label class="block text-sm font-medium mb-1">Caption</label>
                        <input name="caption" class="w-full rounded border panel p-2 bg-transparent" value="{{ $a->caption }}">
                      </div>
                      <div>
                        <label class="block text-sm font-medium mb-1">Fonte</label>
                        <input name="source" class="w-full rounded border panel p-2 bg-transparent" value="{{ $a->source }}">
                      </div>
                    </div>

                    <div class="mt-3">
                      <button class="rounded px-3 py-1 text-white brand">Salvar</button>
                    </div>
                  </form>
                </div>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @empty
    <div class="rounded panel border p-4 muted">Nenhum arquivo enviado ainda.</div>
  @endforelse

  <div class="mt-4">
    <a class="rounded border px-4 py-2 panel-2" href="{{ route('autor.submissions.edit',$submission) }}">Voltar</a>
  </div>
@endsection

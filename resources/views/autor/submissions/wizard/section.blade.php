@extends('console.layout-author')
@section('title','Editar Seção — Wizard')
@section('page.title','Editar Seção')

@push('head')
  <style>
    .trv-input,.trv-select,.trv-textarea{
      width:100%; border:1px solid var(--line); background:var(--panel); color:var(--text);
      border-radius:.75rem; padding:.5rem .75rem; transition:box-shadow .15s,border-color .15s,background .15s,color .15s;
    }
    .trv-input:focus,.trv-select:focus,.trv-textarea:focus{
      outline:none; border-color:var(--brand); box-shadow:0 0 0 3px rgba(225,29,72,.22);
    }
    .trv-label{display:block;font-size:.875rem;font-weight:600;margin-bottom:.25rem}
    .trv-help{font-size:.75rem;color:var(--muted)}
    .btn{border-radius:.75rem;padding:.5rem 1rem;font-weight:600}
    .btn-neutral{border:1px solid var(--line);background:transparent;color:var(--text)}
    .btn-neutral:hover{background:rgba(127,127,127,.06)}
    .btn-brand{background:var(--brand);color:#fff}
    .btn-brand:hover{background:var(--brand-700)}
  </style>
@endpush

@section('content')
@php use Illuminate\Support\Str; @endphp

{{-- Alerts --}}
@if (session('ok'))
  <div class="mb-4 p-3 rounded-xl" style="background:rgba(16,185,129,.12); color:var(--text); border:1px solid var(--line)">{{ session('ok') }}</div>
@endif
@if (session('error'))
  <div class="mb-4 p-3 rounded-xl" style="background:rgba(225,29,72,.12); color:var(--text); border:1px solid var(--line)">{{ session('error') }}</div>
@endif
@if ($errors->any())
  <div class="mb-4 p-3 rounded-xl" style="background:rgba(225,29,72,.12); color:var(--text); border:1px solid var(--line)">
    <ul class="list-disc ml-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<div class="rounded-2xl panel border p-5">
  <div class="flex items-start justify-between gap-4">
    <div class="min-w-0">
      <h1 class="text-xl font-bold truncate" title="{{ $sub->title }}">{{ $sub->title }}</h1>
      <div class="text-sm muted">{{ $sub->type_label ?? $sub->tipo_trabalho }}</div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
      @if ($prevId)
        <a href="{{ route('autor.submissions.section.edit', [$sub,$prevId]) }}" class="btn btn-neutral">Anterior</a>
      @endif
      @if ($nextId)
        <a href="{{ route('autor.submissions.section.edit', [$sub,$nextId]) }}" class="btn btn-neutral">Próxima</a>
      @endif
    </div>
  </div>

  {{-- Form principal da seção (PUT) --}}
  <form method="POST"
        action="{{ route('autor.submissions.section.update', [$sub,$sec]) }}"
        class="mt-6 space-y-4">
    @csrf
    @method('PUT') {{-- ✅ precisa casar com a rota PUT --}}

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div class="md:col-span-4">
        <label class="trv-label" for="sec-title">Título da seção</label>
        <input id="sec-title" name="title" value="{{ old('title',$sec->title) }}" class="trv-input">
      </div>
      <div class="md:col-span-1">
        <label class="trv-label" for="sec-numbering">Numeração (opcional)</label>
        <input id="sec-numbering" name="numbering" value="{{ old('numbering',$sec->numbering) }}" class="trv-input" placeholder="ex.: 1.2, S1">
      </div>
    </div>

    <div>
      <label class="trv-label" for="sec-content">Conteúdo</label>
      <textarea id="sec-content" name="content" rows="12" class="trv-textarea" placeholder="Escreva o conteúdo da seção... (Markdown/HTML)">{{ old('content',$sec->content) }}</textarea>
      <p class="trv-help mt-1">Use anexos abaixo para enriquecer esta seção.</p>
    </div>

    <div class="flex flex-wrap items-center gap-6">
      {{-- Envia sempre 0/1 para não perder estado quando desmarcar --}}
      <input type="hidden" name="show_number" value="0">
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="show_number" value="1" @checked(old('show_number',$sec->show_number))>
        Mostrar número nesta seção
      </label>

      <input type="hidden" name="show_in_toc" value="0">
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="show_in_toc" value="1" @checked(old('show_in_toc',$sec->show_in_toc))>
        Mostrar no sumário (ToC)
      </label>
    </div>

    <div class="flex flex-wrap items-center gap-3">
      @if ($prevId)
        <button name="nav" value="prev" class="btn btn-neutral">Salvar e voltar</button>
      @endif
      <button name="nav" value="stay" class="btn btn-neutral">Salvar</button>
      @if ($nextId)
        <button name="nav" value="next" class="btn btn-brand">Salvar e próximo</button>
      @endif
    </div>
  </form>
</div>

{{-- Assets da seção --}}
<div class="mt-6 rounded-2xl panel border p-5">
  <h2 class="text-lg font-semibold mb-3">Imagens / Tabelas / Anexos desta seção</h2>

  <form method="POST"
        action="{{ route('autor.submissions.assets.store',$sub) }}"
        enctype="multipart/form-data"
        class="space-y-3"
        aria-label="Formulário de anexos">
    @csrf
    <input type="hidden" name="section_id" value="{{ $sec->id }}">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div>
        <label class="trv-label" for="asset-type">Tipo</label>
        <select id="asset-type" name="type" class="trv-select">
          <option value="figure">Figura (imagem)</option>
          <option value="table">Tabela (arquivo)</option>
          <option value="attachment">Anexo</option>
        </select>
      </div>
      <div>
        <label class="trv-label" for="asset-order">Ordem</label>
        <input id="asset-order" type="number" name="order" min="1" value="1" class="trv-input">
      </div>
      <div class="md:col-span-2">
        <label class="trv-label" for="asset-file">Arquivo</label>
        <input id="asset-file" type="file" name="file" class="trv-input" accept="image/*,.pdf,.csv,.xlsx,.xls,.doc,.docx,.zip">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div>
        <label class="trv-label" for="asset-caption">Legenda/Descrição</label>
        <input id="asset-caption" name="caption" class="trv-input" placeholder="Legenda curta">
      </div>
      <div>
        <label class="trv-label" for="asset-source">Fonte (opcional)</label>
        <input id="asset-source" name="source" class="trv-input" placeholder="Ex.: base de dados, autor, URL">
      </div>
    </div>

    <div class="flex justify-end">
      <button class="btn btn-neutral">Anexar</button>
    </div>
  </form>

  <div class="mt-5 space-y-3">
    @forelse ($assets as $a)
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-xl border p-3" style="border-color:var(--line)">
        <div class="min-w-0">
          <div class="font-medium">
            {{ ucfirst($a->type) }} @if($a->order) #{{ $a->order }} @endif
          </div>
          @if ($a->caption)
            <div class="text-sm" style="color:var(--text)">{{ $a->caption }}</div>
          @endif
          @if ($a->file_path)
            <div class="text-xs muted mt-1 truncate">{{ \Illuminate\Support\Str::afterLast($a->file_path,'/') }}</div>
          @endif
        </div>
        <form method="POST"
              action="{{ route('autor.submissions.assets.destroy', [$sub,$a->id]) }}"
              onsubmit="return confirm('Remover este anexo?');"
              class="shrink-0">
          @csrf @method('DELETE')
          <button class="btn btn-neutral">Excluir</button>
        </form>
      </div>
    @empty
      <div class="text-sm muted">Nenhum anexo nesta seção.</div>
    @endforelse
  </div>
</div>

@if (Str::of($sec->title)->lower()->contains('referenc'))
  <div class="mt-6 rounded-2xl panel border p-5">
    <h2 class="text-lg font-semibold mb-3">Referências</h2>

    <form method="POST" action="{{ route('autor.submissions.refs.store', $sub) }}" class="space-y-3">
      @csrf
      <div>
        <label class="trv-label" for="ref-raw">Referência (texto completo)</label>
        <textarea id="ref-raw" name="raw" rows="3" class="trv-textarea" placeholder="Sobrenome, N. (Ano). Título..."></textarea>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
          <label class="trv-label" for="ref-doi">DOI</label>
          <input id="ref-doi" name="doi" class="trv-input" placeholder="10.xxxx/xyz">
        </div>
        <div>
          <label class="trv-label" for="ref-url">URL</label>
          <input id="ref-url" name="url" class="trv-input" placeholder="https://...">
        </div>
        <div>
          <label class="trv-label" for="ref-order">Ordem</label>
          <input id="ref-order" type="number" name="order" min="1" value="1" class="trv-input">
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="trv-label" for="ref-citekey">Citekey</label>
          <input id="ref-citekey" name="citekey" class="trv-input" placeholder="SOBRENOME2025">
        </div>
        <div>
          <label class="trv-label" for="ref-accessed">Acessado em</label>
          <input id="ref-accessed" type="date" name="accessed_at" class="trv-input">
        </div>
      </div>
      <div class="flex justify-end">
        <button class="btn btn-neutral">Adicionar referência</button>
      </div>
    </form>

    @php $refs = $sub->references; @endphp
    <div class="mt-4 space-y-2">
      @forelse ($refs as $ref)
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 rounded-xl border p-3" style="border-color:var(--line)">
          <div class="text-sm min-w-0">
            <div class="font-medium">[{{ $ref->order }}] {{ $ref->citekey ?? 's/ citekey' }}</div>
            <div class="break-words">{{ $ref->raw }}</div>
            @if($ref->doi || $ref->url)
              <div class="text-xs muted mt-1">DOI: {{ $ref->doi ?? '—' }} · URL: {{ $ref->url ?? '—' }}</div>
            @endif
          </div>
          <form method="POST"
                action="{{ route('autor.submissions.refs.destroy', [$sub,$ref->id]) }}"
                onsubmit="return confirm('Remover esta referência?');"
                class="shrink-0">
            @csrf @method('DELETE')
            <button class="btn btn-neutral">Excluir</button>
          </form>
        </div>
      @empty
        <div class="text-sm muted">Nenhuma referência inserida.</div>
      @endforelse
    </div>
  </div>
@endif
@endsection

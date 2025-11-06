@extends('console.layout-author')
@section('title','Wizard da Submissão')
@section('page.title','Wizard da Submissão')

@push('head')
  <style>
    .trv-input,.trv-select,.trv-textarea{
      width:100%; border:1px solid var(--line); background:var(--panel); color:var(--text);
      border-radius:0.75rem; padding:.5rem .75rem; transition:box-shadow .15s,border-color .15s,background .15s,color .15s;
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
  @php use App\Models\Submission; @endphp

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

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- =================== COLUNA PRINCIPAL =================== --}}
    <div class="lg:col-span-2 space-y-6">

      {{-- Progresso das seções --}}
      <div class="rounded-2xl panel border p-5">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-xl font-bold truncate" title="{{ $sub->title }}">{{ $sub->title }}</h1>
          <span class="text-xs px-2 py-0.5 rounded-full chip">{{ $sub->type_label ?? $sub->tipo_trabalho }}</span>
        </div>

        <h2 class="text-sm font-semibold muted mb-2">Progresso das seções</h2>
        <div class="space-y-2">
          @foreach ($sections as $sec)
            @php
              $filled = (bool) ($sec->is_filled ?? false);
              $border = $filled ? 'var(--brand)' : 'var(--line)';
              $pillBg = $filled ? 'var(--brand)' : 'var(--chip)';
              $pillTx = $filled ? '#fff' : 'var(--text)';
            @endphp
            <a href="{{ route('autor.submissions.section.edit', [$sub, $sec]) }}"
               class="flex items-center justify-between rounded-xl border px-3 py-2 hover:opacity-90"
               style="border-color: {{ $border }};">
              <div class="flex items-center gap-2 min-w-0">
                <span class="inline-flex w-5 h-5 items-center justify-center rounded-full text-[11px]"
                      style="background: {{ $pillBg }}; color: {{ $pillTx }};">
                  {{ $loop->iteration }}
                </span>
                <span class="font-medium truncate">
                  @if($sec->numbering) <span class="muted mr-1">{{ $sec->numbering }}</span>@endif
                  {{ $sec->title }}
                </span>
              </div>
              <span class="text-xs {{ $filled ? '' : 'muted' }}">{{ $filled ? 'Preenchida' : 'Pendente' }}</span>
            </a>
          @endforeach
        </div>

        @if ($first)
          <div class="mt-4">
            <a href="{{ route('autor.submissions.section.edit', [$sub, $first]) }}" class="btn btn-neutral">Começar / Continuar</a>
          </div>
        @endif
      </div>

      {{-- Enviar para triagem --}}
      <div class="rounded-2xl panel border p-5">
        <h2 class="text-lg font-semibold mb-2">Enviar para triagem</h2>
        <p class="text-sm muted mb-4">
          Revise título e resumo, preencha pelo menos uma seção com conteúdo, e selecione uma categoria.
        </p>
        <form method="POST" action="{{ route('autor.submissions.submit',$sub) }}"
              onsubmit="return confirm('Confirmar envio para triagem editorial?');">
          @csrf
          <button class="btn btn-brand">Enviar submissão</button>
        </form>
      </div>
    </div>

    {{-- =================== COLUNA LATERAL =================== --}}
    <div class="space-y-6">

      {{-- Metadados (POST) --}}
      <div class="rounded-2xl panel border p-5">
        <h3 class="font-semibold mb-3">Metadados</h3>

        <form method="POST" action="{{ route('autor.submissions.metadata.update', $sub) }}" class="space-y-3">
          @csrf
          {{-- sem PATCH; controller trata POST para metadados quando não há context=categories --}}

          <div>
            <label class="trv-label">Título</label>
            <input name="title" value="{{ old('title',$sub->title) }}" class="trv-input">
          </div>

          <div>
            <label class="trv-label">Resumo</label>
            <textarea name="abstract" rows="4" class="trv-textarea">{{ old('abstract',$sub->abstract) }}</textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="trv-label">Idioma</label>
              <input name="language" value="{{ old('language',$sub->language) }}" class="trv-input">
            </div>
            <div>
              <label class="trv-label">Palavras-chave (separe por vírgula)</label>
              <input name="keywords" value="{{ old('keywords', implode(', ', (array)$sub->keywords)) }}" class="trv-input">
            </div>
          </div>

          <div>
            <label class="trv-label">Tipo</label>
            @php $types=\App\Models\Submission::TYPE_LABELS ?? []; @endphp
            <select name="tipo_trabalho" class="trv-select">
              @forelse($types as $k=>$label)
                <option value="{{ $k }}" @selected($sub->tipo_trabalho===$k)>{{ $label }}</option>
              @empty
                <option value="{{ $sub->tipo_trabalho }}" selected>{{ $sub->tipo_trabalho }}</option>
              @endforelse
            </select>
            <p class="trv-help mt-1">Ao mudar o tipo, as seções padrão são ajustadas automaticamente (conteúdo existente é preservado quando possível).</p>
          </div>

          <div class="flex justify-end">
            <button class="btn btn-neutral">Salvar</button>
          </div>
        </form>
      </div>

      {{-- Categoria (única e obrigatória) --}}
      <div class="rounded-2xl panel border p-5">
        <h3 class="font-semibold mb-3">Categoria</h3>

        <form method="POST" action="{{ route('autor.submissions.metadata.update', $sub) }}" class="space-y-3">
          @csrf
          <input type="hidden" name="context" value="categories">

          @php
            $oldCategoryId = old('category_id', $primaryCategoryId ?? null);
          @endphp

          @if(isset($allCats) && $allCats->count())
            <div class="max-h-64 overflow-auto rounded-xl border" style="border-color:var(--line)">
              <ul class="divide-y" style="border-color:var(--line)">
                @foreach ($allCats as $c)
                  <li class="flex items-center justify-between gap-3 px-3 py-2">
                    <label class="flex items-center gap-2 w-full cursor-pointer">
                      <input type="radio" name="category_id" value="{{ $c->id }}" @checked($oldCategoryId == $c->id)>
                      <span class="text-sm">{{ $c->name }}</span>
                    </label>
                  </li>
                @endforeach
              </ul>
            </div>

            @error('category_id')
              <div class="mt-2 text-xs" style="color:#dc2626">{{ $message }}</div>
            @enderror

            <p class="trv-help mt-1">
              Selecione <strong>uma</strong> categoria. É obrigatório para o envio à triagem.
            </p>

            <div class="flex justify-end mt-3">
              <button class="px-4 py-2 rounded-xl border panel hover:opacity-90">Salvar categoria</button>
            </div>
          @else
            <p class="trv-help">Nenhuma categoria disponível ainda. Peça ao Admin para cadastrar em <em>Admin → Categorias</em>.</p>
          @endif
        </form>
      </div>

      {{-- Seções padrão do tipo (reaplicar) --}}
      @if (Route::has('autor.submissions.sections.reset'))
        <div class="rounded-2xl panel border p-5">
          <h3 class="font-semibold mb-2">Seções padrão do tipo</h3>
          <p class="text-sm muted mb-3">
            Reaplicar o esqueleto do tipo atual. A opção “suave” apenas cria/reordena seções necessárias. “Hard reset” remove seções vazias que não pertencem ao tipo.
          </p>

          <div class="flex items-center gap-2">
            {{-- Suave (default) --}}
            <form method="POST" action="{{ route('autor.submissions.sections.reset', $sub) }}"
                  onsubmit="return confirm('Reaplicar seções (modo suave)?');">
              @csrf
              <button class="btn btn-neutral">Reaplicar (suave)</button>
            </form>

            {{-- Hard reset --}}
            <form method="POST" action="{{ route('autor.submissions.sections.reset', $sub) }}"
                  onsubmit="return confirm('Hard reset: remover seções vazias que não pertencem ao tipo. Continuar?');">
              @csrf
              <input type="hidden" name="mode" value="hard">
              <button class="btn btn-neutral">Hard reset</button>
            </form>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection

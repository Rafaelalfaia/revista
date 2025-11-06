@extends('console.layout-author')
@section('title','Nova Submissão — Autor')
@section('page.title','Nova Submissão')

@push('head')
  <style>
    /* Campos que seguem os tokens do layout (modo claro/escuro) */
    .trv-input, .trv-select, .trv-textarea{
      width:100%;
      border:1px solid var(--line);
      background:var(--panel);
      color:var(--text);
      border-radius:0.75rem; /* rounded-xl */
      padding:0.5rem 0.75rem; /* px-3 py-2 */
      transition: box-shadow .15s ease, border-color .15s ease, background .15s ease, color .15s ease;
    }
    .trv-input:focus, .trv-select:focus, .trv-textarea:focus{
      outline: none;
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(225,29,72,.22); /* brand glow */
    }
    .trv-help{ font-size:.75rem; color:var(--muted); }
    .trv-label{ display:block; font-size:.875rem; font-weight:600; margin-bottom:.25rem; }
    .trv-actions .btn{
      border-radius:0.75rem; padding:.5rem 1rem; font-weight:600;
    }
    .btn-neutral{
      border:1px solid var(--line); background:transparent; color:var(--text);
    }
    .btn-neutral:hover{ background:rgba(127,127,127,.06); }
    .btn-brand{ background:var(--brand); color:#fff; }
    .btn-brand:hover{ background:var(--brand-700); }
    .field{ margin-bottom:1.25rem; }
    .grid-2{ display:grid; gap:1rem; }
    @media (min-width:768px){ .grid-2{ grid-template-columns:1fr 1fr; } }
  </style>
@endpush

@section('content')
  @if ($errors->any())
    <div class="mb-4 p-3 rounded-lg" style="background:rgba(225,29,72,.10); color:#fff; color:var(--text); border:1px solid var(--line);">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  @php $types = \App\Models\Submission::TYPE_LABELS; @endphp

  <form method="POST" action="{{ route('autor.submissions.store') }}">
    @csrf

    <div class="field">
      <label class="trv-label">Título</label>
      <input type="text" name="title" required value="{{ old('title') }}" class="trv-input" placeholder="Digite o título do manuscrito">
    </div>

    <div class="field">
      <label class="trv-label">Tipo de manuscrito</label>
      <select name="tipo_trabalho" class="trv-select">
        @foreach ($types as $k => $label)
          <option value="{{ $k }}" @selected(old('tipo_trabalho')===$k)>{{ $label }}</option>
        @endforeach
      </select>
    </div>

    <div class="field">
      <label class="trv-label">Resumo</label>
      <textarea name="abstract" rows="6" class="trv-textarea" placeholder="Escreva um resumo claro e objetivo (150–250 palavras recomendado)">{{ old('abstract') }}</textarea>
      <p class="trv-help mt-1">Dica: seja específico sobre objetivo, método, resultados e conclusões.</p>
    </div>

    <div class="grid-2">
      <div class="field">
        <label class="trv-label">Idioma</label>
        <input type="text" name="language" value="{{ old('language','pt-BR') }}" class="trv-input" placeholder="pt-BR, en-US...">
      </div>
      <div class="field">
        <label class="trv-label">Palavras-chave (separadas por vírgula)</label>
        <input type="text" name="keywords" value="{{ old('keywords') }}" class="trv-input" placeholder="ex.: educação, IA, avaliação">
      </div>
    </div>

    <div class="trv-actions flex items-center gap-3 mt-6">
      <a href="{{ route('autor.submissions.index') }}" class="btn btn-neutral">Cancelar</a>
      <button class="btn btn-brand">Criar e começar o wizard</button>
    </div>
  </form>
@endsection

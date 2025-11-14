@extends('console.layout-author')
@section('title','Nova Submissão · Autor')
@section('page.title','Nova Submissão')

@push('head')
<style>
  .shell-author-create{display:flex;flex-direction:column;gap:1rem}
  .flash-err{border-radius:.9rem;border:1px solid rgba(248,113,113,.5);background:rgba(254,226,226,1);padding:.65rem .8rem;font-size:.8rem;color:#b91c1c}
  .flash-err ul{margin-left:1.1rem}
  .form-card{border-radius:1.3rem;border:1px solid var(--line);background:radial-gradient(circle at top left,rgba(251,113,133,.16),transparent 55%),radial-gradient(circle at top right,rgba(59,130,246,.2),transparent 55%),var(--panel);overflow:hidden}
  .form-head{padding:.9rem 1rem 1.1rem;border-bottom:1px solid rgba(148,163,184,.4);display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem}
  .form-title{font-size:1rem;font-weight:700}
  .form-sub{font-size:.78rem;color:var(--muted);margin-top:.1rem}
  .form-pill{margin-top:.35rem;font-size:.7rem;border-radius:999px;padding:.1rem .6rem;background:var(--soft);border:1px solid var(--line);display:inline-flex;align-items:center;gap:.25rem}
  .form-body{padding:1rem 1rem 1.1rem;display:flex;flex-direction:column;gap:1rem}
  .field-grid{display:grid;gap:1rem}
  @media(min-width:768px){.field-grid-2{grid-template-columns:minmax(0,1fr) minmax(0,1fr)}}
  .field{display:flex;flex-direction:column;gap:.25rem}
  .field-label{font-size:.85rem;font-weight:600}
  .field-help{font-size:.75rem;color:var(--muted)}
  .input-app,.select-app,.textarea-app{width:100%;border-radius:.9rem;border:1px solid var(--line);background:var(--panel);color:var(--text);padding:.6rem .8rem;font-size:.85rem;transition:border-color .15s,box-shadow .15s,background .15s,color .15s}
  .input-app:focus,.select-app:focus,.textarea-app:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(225,29,72,.22)}
  .textarea-app{min-height:8rem;resize:vertical}
  .badge-required{font-size:.75rem;color:var(--muted);margin-left:.25rem}
  .badge-required span{color:var(--brand)}
  .actions-row{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:flex-end;margin-top:.5rem}
  .btn-primary{border-radius:.9rem;border:none;padding:.55rem 1.15rem;font-size:.85rem;font-weight:600;background:var(--brand);color:#fff;display:inline-flex;align-items:center;gap:.35rem}
  .btn-secondary{border-radius:.9rem;border:1px solid var(--line);padding:.55rem 1rem;font-size:.82rem;font-weight:500;background:var(--panel);display:inline-flex;align-items:center;gap:.3rem}
  .chip-count{font-size:.75rem;border-radius:999px;padding:.1rem .6rem;background:var(--soft);border:1px solid var(--line);color:var(--muted)}
</style>
@endpush

@section('content')
@php
  $types = \App\Models\Submission::TYPE_LABELS;
  $cats  = \App\Models\Category::orderBy('name')->get(['id','name']);
  $catsSelected = collect(old('categories',[]))->map(fn($v)=>(int)$v)->all();
@endphp

<div class="shell-author-create">
  @if ($errors->any())
    <div class="flash-err">
      <strong>Revise os campos antes de continuar:</strong>
      <ul class="list-disc mt-1">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('autor.submissions.store') }}" class="form-card" x-data="submissionCreate()">
    @csrf
    <input type="hidden" name="language" value="pt-BR">

    <div class="form-head">
      <div>
        <div class="form-title">Criar nova submissão</div>
        <div class="form-sub">Preencha os dados iniciais do manuscrito. Você poderá editar tudo depois no wizard.</div>
        <div class="form-pill">
          <span>Novo projeto de artigo</span>
        </div>
      </div>
      <div class="text-right hidden sm:block">
        <div class="badge-required"><span>*</span> campos obrigatórios</div>
        <div class="chip-count mt-1">
          {{ count($catsSelected) }} categoria{{ count($catsSelected) === 1 ? '' : 's' }} selecionada{{ count($catsSelected) === 1 ? '' : 's' }}
        </div>
      </div>
    </div>

    <div class="form-body">
      <div class="field">
        <label for="title" class="field-label">
          Título <span class="text-rose-500">*</span>
        </label>
        <input id="title"
               type="text"
               name="title"
               required
               value="{{ old('title') }}"
               class="input-app"
               placeholder="Digite o título do manuscrito">
        <p class="field-help">Use um título claro, específico e alinhado ao conteúdo do estudo.</p>
      </div>

      <div class="field-grid field-grid-2">
        <div class="field">
          <label for="tipo_trabalho" class="field-label">
            Tipo de manuscrito <span class="text-rose-500">*</span>
          </label>
          <select id="tipo_trabalho" name="tipo_trabalho" class="select-app" required>
            @foreach ($types as $k => $label)
              <option value="{{ $k }}" @selected(old('tipo_trabalho')===$k)>{{ $label }}</option>
            @endforeach
          </select>
          <p class="field-help">Escolha o formato que melhor descreve o seu trabalho.</p>
        </div>

        <div class="field">
          <label for="keywords" class="field-label">Palavras-chave</label>
          <input id="keywords"
                 type="text"
                 name="keywords"
                 value="{{ old('keywords') }}"
                 class="input-app"
                 placeholder="Ex.: educação, IA, avaliação">
          <p class="field-help">Separe por vírgulas. Exemplo: avaliação, ensino, tecnologia.</p>
        </div>
      </div>

      <div class="field">
        <label for="abstract" class="field-label">Resumo</label>
        <textarea id="abstract"
                  name="abstract"
                  rows="6"
                  class="textarea-app"
                  placeholder="Escreva um resumo claro e objetivo (150–250 palavras recomendado)">{{ old('abstract') }}</textarea>
        <p class="field-help">Inclua objetivo, método, principais resultados e conclusões.</p>
      </div>

      <div class="field-grid field-grid-2">
        <div class="field">
          <label for="categories" class="field-label">
            Categorias <span class="text-rose-500">*</span>
          </label>
          <select id="categories"
                  name="categories[]"
                  class="select-app"
                  multiple
                  size="6"
                  required
                  x-ref="categories"
                  @change="syncPrimary()">
            @foreach ($cats as $c)
              <option value="{{ $c->id }}" @selected(in_array($c->id,$catsSelected,true))>{{ $c->name }}</option>
            @endforeach
          </select>
          <p class="field-help">Selecione uma ou mais áreas relacionadas ao seu manuscrito.</p>
        </div>

        <div class="field">
          <label for="primary_category_id" class="field-label">
            Categoria principal <span class="text-rose-500">*</span>
          </label>
          <select id="primary_category_id"
                  name="primary_category_id"
                  class="select-app"
                  required
                  x-ref="primary">
            <option value="">Selecione</option>
            @foreach ($cats as $c)
              <option value="{{ $c->id }}" @selected(old('primary_category_id')==$c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
          <p class="field-help">Usada para atribuição automática ao revisor mais adequado.</p>
        </div>
      </div>

      <div class="actions-row">
        <a href="{{ route('autor.submissions.index') }}" class="btn-secondary">
          Cancelar
        </a>
        <button type="submit" class="btn-primary">
          Criar e começar o projeto
        </button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  function submissionCreate(){
    return {
      syncPrimary(){
        var cat = this.$refs.categories;
        var primary = this.$refs.primary;
        if(!cat || !primary) return;
        var selected = Array.prototype.slice.call(cat.selectedOptions).map(function(o){return o.value;});
        Array.prototype.slice.call(primary.options).forEach(function(opt){
          if(opt.value === ''){
            return;
          }
          var ok = selected.indexOf(opt.value) !== -1;
          opt.disabled = !ok;
          opt.hidden = !ok;
        });
        if(selected.indexOf(primary.value) === -1){
          primary.value = '';
        }
      }
    };
  }

  document.addEventListener('DOMContentLoaded', function(){
    var compEl = document.querySelector('form[x-data]');
    if(!compEl || !window.Alpine) return;
    var comp = Alpine.$data(compEl);
    if(comp && typeof comp.syncPrimary === 'function'){
      comp.syncPrimary();
    }
  });
</script>
@endpush

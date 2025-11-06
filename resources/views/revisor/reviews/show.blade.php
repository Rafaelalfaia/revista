@extends('console.layout')
@section('title','Leitura — Revisão #'.$review->id)
@section('page.title','Revisão')

@push('context')
  @php
    $sub   = $review->submission;
    $map   = ['atribuida'=>'Atribuída','em_revisao'=>'Em revisão','revisao_solicitada'=>'Correções solicitadas','parecer_enviado'=>'Parecer enviado'];
    $s     = $review->status;
    $label = $map[$s] ?? ucfirst(str_replace('_',' ', $s));
  @endphp
  <div class="flex items-center justify-between">
    <div class="min-w-0">
      <div class="text-xs muted">Submissão</div>
      <div class="font-medium truncate" title="{{ $sub->title }}">{{ $sub->title }}</div>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-2 py-0.5 rounded-md text-xs chip">{{ $label }}</span>
      <a href="{{ route('revisor.reviews.index') }}" class="inline-flex items-center rounded-lg px-3 h-9 text-sm border panel"
         style="border-color:var(--line)">Voltar</a>
    </div>
  </div>
@endpush

@push('head')
<style>
  /* usa as vars do console.layout */
  .reader{border:1px solid var(--line);border-radius:1rem;background:var(--panel);padding:1rem;}
  .gallery img{display:block;max-width:100%;height:auto;border-radius:.5rem;}
  .gallery figure{border:1px solid var(--line);border-radius:.75rem;padding:.5rem;background:var(--panel);}
  .gallery figcaption{font-size:.75rem;color:var(--muted);margin-top:.25rem;}

  /* highlight compatível claro/escuro */
  .cm-hi{
    background: color-mix(in oklab, var(--brand) 22%, transparent);
    border-radius:.25rem; padding:.05rem .15rem;
  }
  .pin{animation:pinPulse 1s ease-in-out 1;}
  @keyframes pinPulse{
    0%{outline:3px solid rgba(0,0,0,0)}
    50%{outline:3px solid color-mix(in oklab, var(--brand) 45%, transparent)}
    100%{outline:3px solid rgba(0,0,0,0)}
  }

  /* Campos de formulário coerentes com o console (claro/escuro) */
  .panel select,
  .panel textarea,
  .panel input[type="text"],
  .panel input[type="email"],
  .panel input[type="number"],
  .panel input[type="search"],
  .panel input[type="password"]{
    background: var(--panel);
    color: var(--text);
    border: 1px solid var(--line);
    border-radius: .75rem;
    padding: .5rem .75rem;
  }
  .panel ::placeholder{ color: var(--muted); opacity:.9; }

  /* Foco consistente */
  .panel :is(input,select,textarea):focus{
    outline: 0;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px color-mix(in oklab, var(--brand) 22%, transparent);
  }

  /* Menu do select em alguns navegadores */
  .panel select option{
    background: var(--panel);
    color: var(--text);
  }


</style>
@endpush

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp

<div class="grid gap-4 md:grid-cols-12">
  {{-- Leitura com âncoras de seção + galeria --}}
  <div class="md:col-span-8 reader">
    @foreach($sub->rootSections as $sec)
      <section id="sec-{{ $sec->id }}" data-section-id="{{ $sec->id }}" class="mb-8">
        <h3 class="font-semibold text-lg mb-2">{{ $sec->title }}</h3>

        {{-- Conteúdo seguro (o highlight é via JS) --}}
        <div class="readable-content" data-content-for="{{ $sec->id }}">
          {!! nl2br(e($sec->content)) !!}
        </div>

        @php
          $imgs = $sub->assets
              ->where('section_id', $sec->id)
              ->where('type','figure')
              ->sortBy('order');
        @endphp
        @if($imgs->count())
          <div class="gallery grid grid-cols-2 md:grid-cols-3 gap-3 mt-3">
            @foreach($imgs as $a)
              <figure>
                <img src="{{ Storage::url($a->file_path) }}" alt="{{ $a->caption ?? 'Figura' }}">
                @if($a->caption)<figcaption>{{ $a->caption }}</figcaption>@endif
              </figure>
            @endforeach
          </div>
        @endif
      </section>
    @endforeach
  </div>

  {{-- Painel lateral: Comentários + Parecer --}}
  <div class="md:col-span-4 space-y-4">
    {{-- painel de comentários (com delete, autor resolve, revisor verifica) --}}
    @include('submissions.partials.comments-panel', [
      'submission' => $sub,
      'review'     => $review,
    ])

    {{-- Parecer --}}
    <div class="border rounded-xl panel p-3" style="border-color:var(--line)">
      <div class="font-semibold mb-2">Parecer</div>
      <form method="POST" action="{{ route('revisor.reviews.submitOpinion', $review) }}">
        @csrf
        <div class="grid gap-2">
          <select name="recommendation">
            <option value="aprovar">Aprovar</option>
            <option value="rejeitar">Rejeitar</option>
            <option value="revisar">Solicitar revisões</option>
            </select>

            <textarea name="notes" rows="3" placeholder="Observações (opcional)"></textarea>

        </div>
        <div class="mt-3 text-right">
          <button class="px-3 py-2 rounded text-white brand">Enviar parecer</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- JS: captura seleção + highlight + pular para trecho --}}
<script>
(function(){
  // Preenche section_id + excerpt ao selecionar texto
  document.addEventListener('mouseup', () => {
    const sel = window.getSelection();
    const txt = sel ? sel.toString().trim() : '';
    if (!txt) return;

    let el = sel.anchorNode;
    while (el && el.nodeType !== 1) el = el.parentNode;
    while (el && !(el.dataset && el.dataset.sectionId)) el = el.parentNode;

    const form = document.querySelector('form[action$="{{ route('comments.store', $sub) }}"]');
    if (form && el && el.dataset.sectionId) {
      const sidInput = form.querySelector('input[name="section_id"]');
      const exInput  = form.querySelector('textarea[name="excerpt"]');
      if (sidInput) sidInput.value = el.dataset.sectionId;
      if (exInput)  exInput.value  = txt;
    }
  });

  // escapa regex básico
  const esc = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

  // marca 1ª ocorrência do trecho na seção
  function markInSection(sectionId, excerpt, cid){
    if (!excerpt || !sectionId) return null;
    const box = document.querySelector(`.readable-content[data-content-for="${sectionId}"]`);
    if (!box) return null;
    if (box.querySelector(`[data-cid="${cid}"]`)) return box.querySelector(`#cm-${cid}`);

    const re   = new RegExp(esc(excerpt), 'i');
    const html = box.innerHTML;
    if (!re.test(html)) return null;

    box.innerHTML = html.replace(re, (m)=>`<mark class="cm-hi" data-cid="${cid}" id="cm-${cid}">${m}</mark>`);
    return box.querySelector(`#cm-${cid}`);
  }

  // Botão "Ir ao trecho"
  document.addEventListener('click', (ev) => {
    const btn = ev.target.closest('[data-jump]');
    if (!btn) return;
    const cid = btn.dataset.cid, sid = btn.dataset.sid, excerpt = btn.dataset.excerpt || '';
    const mark = document.getElementById(`cm-${cid}`) || markInSection(sid, excerpt, cid);
    if (mark){
      mark.scrollIntoView({behavior:'smooth', block:'center'});
      mark.classList.add('pin'); setTimeout(()=>mark.classList.remove('pin'), 1000);
    } else {
      const sec = document.getElementById(`sec-${sid}`);
      if (sec) sec.scrollIntoView({behavior:'smooth', block:'start'});
    }
  });

  // Realça automaticamente os já existentes
  document.querySelectorAll('[data-comment]').forEach(el=>{
    const cid = el.dataset.cid, sid = el.dataset.sid, excerpt = el.dataset.excerpt || '';
    markInSection(sid, excerpt, cid);
  });
})();
</script>
@endsection

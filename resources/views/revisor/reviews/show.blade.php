@extends('console.layout')
@section('title','Leitura — Revisão #'.$review->id)
@section('page.title','Revisão')

@push('context')
  @php
    $sub   = $review->submission;
    $map   = [
      'atribuida'          => 'Atribuída',
      'em_revisao'         => 'Em revisão',
      'revisao_solicitada' => 'Correções solicitadas',
      'parecer_enviado'    => 'Parecer enviado',
    ];
    $s     = $review->status;
    $label = $map[$s] ?? ucfirst(str_replace('_',' ', $s));
  @endphp

  <div class="flex items-center justify-between gap-3">
    <div class="min-w-0">
      <div class="text-[11px] uppercase tracking-[.18em] muted mb-1">Submissão</div>
      <div class="text-sm font-semibold truncate" title="{{ $sub->title }}">{{ $sub->title }}</div>
    </div>
    <div class="flex items-center gap-2">
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium chip">
        {{ $label }}
      </span>
      <a href="{{ route('revisor.reviews.index') }}"
         class="inline-flex items-center rounded-lg px-3 h-9 text-sm border panel"
         style="border-color:var(--line)">
        Voltar
      </a>
    </div>
  </div>
@endpush

@push('head')
<style>
  .rev-read-shell{
    max-width:1100px;
    margin:0 auto;
    padding:.75rem 0 0;
    display:grid;
    gap:1rem;
    grid-template-columns:minmax(0,1fr);
  }
  @media(min-width:960px){
    .rev-read-shell{
      grid-template-columns:minmax(0,1.6fr) minmax(320px,.9fr);
      align-items:flex-start;
    }
  }

  .rev-reader{
    border:1px solid var(--line);
    border-radius:1.1rem;
    background:var(--panel);
    padding:1rem .9rem 1.1rem;
  }
  @media(min-width:640px){
    .rev-reader{
      padding:1.1rem 1.3rem 1.3rem;
    }
  }

  .rev-sec{
    margin-bottom:1.75rem;
  }
  .rev-sec:last-child{
    margin-bottom:0;
  }
  .rev-sec-title{
    font-size:1rem;
    font-weight:600;
    margin-bottom:.35rem;
  }
    .rev-sec-body{
    font-size:.9rem;
    line-height:1.7;

    word-break: break-word;
    overflow-wrap: anywhere;
  }

  .rev-gallery{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:.6rem;
    margin-top:.75rem;
  }
  @media(min-width:768px){
    .rev-gallery{
      grid-template-columns:repeat(3,minmax(0,1fr));
    }
  }
  .rev-fig{
    border:1px solid var(--line);
    border-radius:.8rem;
    padding:.4rem;
    background:var(--panel-2);
  }
  .rev-fig img{
    display:block;
    max-width:100%;
    height:auto;
    border-radius:.55rem;
  }
  .rev-figcap{
    font-size:.75rem;
    color:var(--muted);
    margin-top:.25rem;
  }

  .rev-side{
    display:flex;
    flex-direction:column;
    gap:.8rem;
  }
  @media(min-width:960px){
    .rev-side-sticky{
      position:sticky;
      top:5rem;
    }
  }

  .rev-box{
    border:1px solid var(--line);
    border-radius:1.1rem;
    background:var(--panel);
    padding:.9rem .9rem 1rem;
  }
  .rev-box-title{
    font-size:.9rem;
    font-weight:600;
    margin-bottom:.4rem;
  }
  .rev-box-sub{
    font-size:.78rem;
    color:var(--muted);
    margin-bottom:.6rem;
  }

  .rev-form-grid{
    display:grid;
    gap:.55rem;
  }
  .rev-input,
  .rev-textarea,
  .rev-select{
    width:100%;
    font-size:.85rem;
    border-radius:.8rem;
    border:1px solid var(--line);
    background:var(--panel-2);
    padding:.5rem .75rem;
    color:var(--text);
  }
  .rev-select{
    height:2.5rem;
  }
  .rev-textarea{
    min-height:96px;
    resize:vertical;
  }
  .rev-input:focus,
  .rev-select:focus,
  .rev-textarea:focus{
    outline:none;
    border-color:var(--brand);
    box-shadow:0 0 0 3px color-mix(in oklab, var(--brand) 22%, transparent);
    background:var(--panel);
  }
  .rev-helper{
    font-size:.75rem;
    color:var(--muted);
  }
  .rev-submit{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:.9rem;
    padding:.55rem 1.1rem;
    font-size:.85rem;
    font-weight:500;
    color:#fff;
    background:var(--brand);
    border:0;
  }
  .rev-submit:hover{
    background:var(--brand-700);
  }

  .cm-hi{
    background:color-mix(in oklab, var(--brand) 22%, transparent);
    border-radius:.25rem;
    padding:.05rem .15rem;
  }
  .pin{
    animation:pinPulse 1s ease-in-out 1;
  }
  @keyframes pinPulse{
    0%{outline:3px solid rgba(0,0,0,0)}
    50%{outline:3px solid color-mix(in oklab, var(--brand) 45%, transparent)}
    100%{outline:3px solid rgba(0,0,0,0)}
  }
</style>
@endpush

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp

<div class="rev-read-shell">
  <div class="rev-reader">
    @foreach($sub->rootSections as $sec)
      <section id="sec-{{ $sec->id }}"
               data-section-id="{{ $sec->id }}"
               class="rev-sec">
        <h3 class="rev-sec-title">{{ $sec->title }}</h3>
        <div class="rev-sec-body readable-content" data-content-for="{{ $sec->id }}">
          {!! nl2br(e($sec->content)) !!}
        </div>

        @php
          $imgs = $sub->assets
              ->where('section_id', $sec->id)
              ->where('type','figure')
              ->sortBy('order');
        @endphp

        @if($imgs->count())
          <div class="rev-gallery">
            @foreach($imgs as $a)
              <figure class="rev-fig">
                <img
                  src="{{ Storage::disk($a->disk ?: 'public')->url($a->file_path) }}"
                  alt="{{ $a->caption ?? 'Figura' }}">
                @if($a->caption)
                  <figcaption class="rev-figcap">{{ $a->caption }}</figcaption>
                @endif
              </figure>
            @endforeach
          </div>
        @endif
      </section>
    @endforeach
  </div>

  <aside class="rev-side">
    <div class="rev-side-sticky space-y-3">
      @include('submissions.partials.comments-panel', ['submission' => $sub,'review' => $review])

      <div class="rev-box">
        <div class="rev-box-title">Parecer</div>
        <div class="rev-box-sub">
          Defina sua recomendação geral e, se quiser, inclua uma mensagem breve para o autor.
        </div>

        <form method="POST" action="{{ route('revisor.reviews.submitOpinion', $review) }}">
          @csrf
          <div class="rev-form-grid">
            <select name="recommendation" class="rev-select">
              <option value="aprovar">Aprovar</option>
              <option value="rejeitar">Rejeitar</option>
              <option value="revisar">Solicitar revisões</option>
            </select>

            <textarea name="notes"
                      class="rev-textarea"
                      rows="3"
                      placeholder="Mensagem de notificação ao autor (opcional)"></textarea>

            <div class="rev-helper">
              Esta mensagem será enviada como notificação ao autor e não entra na lista de correções detalhadas.
            </div>
          </div>

          <div class="mt-3 text-right">
            <button type="submit" class="rev-submit">
              Enviar parecer
            </button>
          </div>
        </form>
      </div>
    </div>
  </aside>
</div>

<script>
(function(){
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

  const esc = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

  function markInSection(sectionId, excerpt, cid){
    if (!excerpt || !sectionId) return null;
    const box = document.querySelector('.readable-content[data-content-for="'+sectionId+'"]');
    if (!box) return null;
    if (box.querySelector('[data-cid="'+cid+'"]')) return box.querySelector('#cm-'+cid);

    const re   = new RegExp(esc(excerpt), 'i');
    const html = box.innerHTML;
    if (!re.test(html)) return null;

    box.innerHTML = html.replace(re, function(m){
      return '<mark class="cm-hi" data-cid="'+cid+'" id="cm-'+cid+'">'+m+'</mark>';
    });

    return box.querySelector('#cm-'+cid);
  }

  document.addEventListener('click', function(ev){
    const btn = ev.target.closest('[data-jump]');
    if (!btn) return;

    const cid = btn.dataset.cid;
    const sid = btn.dataset.sid;
    const excerpt = btn.dataset.excerpt || '';

    const existing = document.getElementById('cm-'+cid);
    const mark = existing || markInSection(sid, excerpt, cid);

    if (mark){
      mark.scrollIntoView({behavior:'smooth', block:'center'});
      mark.classList.add('pin');
      setTimeout(function(){ mark.classList.remove('pin'); }, 1000);
    } else {
      const sec = document.getElementById('sec-'+sid);
      if (sec) sec.scrollIntoView({behavior:'smooth', block:'start'});
    }
  });

  document.querySelectorAll('[data-comment]').forEach(function(el){
    const cid = el.dataset.cid;
    const sid = el.dataset.sid;
    const excerpt = el.dataset.excerpt || '';
    markInSection(sid, excerpt, cid);
  });
})();
</script>
@endsection

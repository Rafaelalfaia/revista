@extends('console.layout')
@section('title','Leitura — Submissão #'.$submission->id)
@section('page.title','Leitura da Submissão')

@push('head')
<style>
  /* layout de leitura */
  .reader-shell { --page-gap: 2rem; }
  .reader-toolbar .btn {
      border-radius: .75rem; padding: .5rem .9rem; font-weight: 600;
      border: 1px solid var(--line); background: var(--panel); color: var(--text);
  }
  .reader-toolbar .btn-brand { background: var(--brand); color: #fff; border-color: transparent; }
  .reader {
    border:1px solid var(--line); background:var(--panel);
    border-radius: 1rem; padding: 2rem; min-height: 70vh;
    overflow: auto;
  }
  .reader.single .readable { column-count: 1; column-gap: var(--page-gap); }
  .reader.dual   .readable { column-count: 2; column-gap: var(--page-gap); }
  .readable h2 { font-size: 1.1rem; font-weight: 700; margin: 0 0 .6rem; }
  .readable p  { margin: .75rem 0; line-height: 1.7; text-align: justify; }
  .readable figure{ margin: .75rem 0; }
  .readable .sec { break-inside: avoid; page-break-inside: avoid; margin-bottom: 1rem; }

  /* destaque de comentários */
  mark[data-cid]{ background: rgba(225,29,72,.25); padding: .1rem .15rem; border-radius: .25rem; }
  .pin {
    position:absolute; transform: translate(-50%,-100%); pointer-events: none;
    background: var(--brand); color:#fff; font-size:.7rem; line-height:1;
    padding:.25rem .45rem; border-radius:.35rem; box-shadow: 0 6px 18px rgba(0,0,0,.25);
  }

  /* popover para comentar */
  .note-pop {
    position: absolute; z-index: 60; width: min(380px, 90vw);
    background: var(--panel); border:1px solid var(--line); border-radius: .9rem; padding: .75rem;
    box-shadow: 0 12px 40px rgba(0,0,0,.25);
  }
  .note-pop textarea{
    width:100%; background:transparent; color:var(--text);
    border:1px solid var(--line); border-radius:.6rem; padding:.5rem .6rem; resize: vertical; min-height: 80px;
  }
  .note-pop .actions{ display:flex; gap:.5rem; justify-content:flex-end; margin-top:.5rem; }
  .note-pop .btn{ border-radius:.6rem; padding:.4rem .7rem; }
</style>
@endpush

@section('content')
<div
  x-data="readerApp({{
    json_encode([
      'csrf'         => csrf_token(),
      'storeUrl'     => route('admin.submissions.comments.store', $submission),
      'indexUrl'     => route('admin.submissions.comments.index', $submission),
      'commentsBase' => route('admin.submissions.comments.index', $submission), // ex: /admin/submissoes/{slug}/comments
    ])
  }})"
  x-init="init()"
  class="reader-shell">

  {{-- toolbar --}}
  <div class="reader-toolbar mb-3 flex flex-wrap items-center gap-2">
    <a href="{{ route('admin.submissions.show', $submission) }}" class="btn">← Voltar</a>

    <div class="ml-auto flex items-center gap-2">
      <button class="btn" :class="mode==='single'?'ring-1 ring-rose-300':''" @click="setMode('single')">1 página</button>
      <button class="btn" :class="mode==='dual'?'ring-1 ring-rose-300':''"   @click="setMode('dual')">2 páginas</button>

      <button class="btn" @click="toggleComment()" :class="commentMode ? 'btn-brand' : ''">
        <span x-text="commentMode ? 'Comentando…' : 'Comentar'"></span>
      </button>

      {{-- solicitar correções (usa rota de transição existente) --}}
      <form method="POST" action="{{ route('admin.submissions.transition',$submission) }}">
        @csrf
        <input type="hidden" name="message" x-ref="globalMessage">
        <button name="action" value="request_fixes" class="btn">Solicitar correções</button>
      </form>
    </div>
  </div>

  {{-- área de leitura --}}
  <div class="reader relative" :class="mode" id="readerContainer">
    <div id="readable" class="readable">
      @foreach($sections as $sec)
        <section class="sec" data-sec-id="{{ $sec->id }}">
          @if($sec->title)
            <h2>
              @if($sec->numbering) <span class="muted mr-1">{{ $sec->numbering }}</span> @endif
              {{ $sec->title }}
            </h2>
          @endif
          @if($sec->content)
            {!! $sec->content !!}
          @else
            <p class="muted">—</p>
          @endif
        </section>
      @endforeach
    </div>

    {{-- popover p/ nova nota --}}
    <div x-show="showPop" x-transition class="note-pop" :style="`top:${popY}px; left:${popX}px`">
      <div class="text-xs muted mb-1" x-text="popHint"></div>
      <textarea x-model="note" placeholder="Descreva a correção/observação ao autor…"></textarea>
      <div class="actions">
        <button class="btn" @click="cancelPop()">Cancelar</button>
        <button class="btn btn-brand" @click="saveNote()">Salvar</button>
      </div>
    </div>
  </div>

  {{-- lista de comentários (resumo) --}}
  <div class="mt-3 rounded-xl panel border p-3 text-sm">
    <div class="font-medium mb-2">Comentários</div>
    <template x-if="comments.length===0">
      <div class="muted">Sem comentários ainda.</div>
    </template>
    <template x-for="c in comments" :key="c.id">
      <div class="border-t first:border-t-0 py-2 flex items-start justify-between gap-3" style="border-color:var(--line)">
        <div>
          <div class="muted text-xs" x-text="`Seção #${c.section_id}`"></div>
          <div class="italic">“<span x-text="c.quote"></span>”</div>
          <div class="mt-1" x-text="c.note"></div>
        </div>
        <div class="shrink-0">
          <button class="text-xs rounded-lg border px-2 py-1 hover:opacity-80"
                  style="border-color:var(--line)"
                  @click="confirmDelete(c.id)">
            apagar
          </button>
        </div>
      </div>
    </template>
  </div>
</div>
@endsection

@push('scripts')
<script>
function readerApp(cfg){
  return {
    mode: localStorage.getItem('trv.reader.mode') || 'single',
    commentMode: false,
    showPop:false, popX:0, popY:0, note:'', selection:null, popHint:'',
    comments:[],

    setMode(m){ this.mode=m; localStorage.setItem('trv.reader.mode', m); },
    toggleComment(){ this.commentMode=!this.commentMode; if(!this.commentMode) this.cancelPop(); },

    async init(){ await this.loadComments(); this.highlightAll(); this.bindSelection(); },

    bindSelection(){
      const host = document.getElementById('readable');
      const container = document.getElementById('readerContainer');
      host.addEventListener('mouseup', () => {
        if(!this.commentMode) return;
        const sel = window.getSelection();
        if(!sel || sel.isCollapsed) { this.cancelPop(); return; }
        const range = sel.getRangeAt(0);

        // identifica a seção
        const secEl = range.startContainer.parentElement?.closest?.('[data-sec-id]');
        if(!secEl) { this.cancelPop(); return; }
        const sectionId = parseInt(secEl.getAttribute('data-sec-id'));

        // texto selecionado
        const quote = sel.toString().trim();
        if(quote.length < 2){ this.cancelPop(); return; }

        // posição do popover baseada no container com overflow
        const rect = range.getBoundingClientRect();
        const cRect = container.getBoundingClientRect();
        const scrollTop  = container.scrollTop;
        const scrollLeft = container.scrollLeft;

        this.popX = Math.min(cRect.width - 380, rect.left - cRect.left + scrollLeft) + 12;
        this.popY = (rect.top  - cRect.top  + scrollTop) + 12;

        this.selection = { section_id: sectionId, quote, page_mode:this.mode };
        this.popHint = `Seção #${sectionId} — ${quote.slice(0,80)}${quote.length>80?'…':''}`;
        this.note = '';
        this.showPop = true;
      });
    },

    cancelPop(){ this.showPop=false; this.selection=null; this.note=''; window.getSelection()?.removeAllRanges?.(); },

    async saveNote(){
      if(!this.selection || this.note.trim().length<2) return alert('Escreva a observação.');
      const payload = { ...this.selection, note: this.note.trim() };

      const resp = await fetch(cfg.storeUrl, {
        method:'POST',
        headers: {
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': cfg.csrf,
          'Accept':'application/json'
        },
        body: JSON.stringify(payload)
      });
      if(!resp.ok){ alert('Falha ao salvar comentário.'); return; }

      const saved = await resp.json();
      this.comments.unshift(saved);
      this.highlightOne(saved);
      this.cancelPop();
    },

    async loadComments(){
      try{
        const resp = await fetch(cfg.indexUrl, { headers:{'Accept':'application/json'}});
        if(resp.ok) this.comments = await resp.json();
      }catch(e){}
    },

    highlightAll(){
      setTimeout(() => { this.comments.forEach(c => this.highlightOne(c)); }, 0);
    },

    highlightOne(c){
      const sec = document.querySelector(`[data-sec-id="${c.section_id}"]`);
      if(!sec || !c.quote) return;
      const walker = document.createTreeWalker(sec, NodeFilter.SHOW_TEXT);
      let node;
      while(node = walker.nextNode()){
        const hay = node.nodeValue.toLowerCase();
        const needle = String(c.quote).toLowerCase();
        const idx = hay.indexOf(needle);
        if(idx >= 0){
          const before = node.nodeValue.slice(0, idx);
          const match  = node.nodeValue.slice(idx, idx + c.quote.length);
          const after  = node.nodeValue.slice(idx + c.quote.length);
          const mark = document.createElement('mark');
          mark.setAttribute('data-cid', c.id);
          mark.textContent = match;
          const frag = document.createDocumentFragment();
          if(before) frag.appendChild(document.createTextNode(before));
          frag.appendChild(mark);
          if(after)  frag.appendChild(document.createTextNode(after));
          node.parentNode.replaceChild(frag, node);
          break;
        }
      }
    },

    /* ==== apagar comentário ==== */
    confirmDelete(id){
      if (confirm('Apagar este comentário?')) this.deleteComment(id);
    },
    async deleteComment(id){
      const url = `${cfg.commentsBase}/${id}`;
      const resp = await fetch(url, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': cfg.csrf,
          'Accept': 'application/json'
        }
      });
      if(!resp.ok){ alert('Falha ao apagar comentário.'); return; }
      this.comments = this.comments.filter(c => c.id !== id);
      this.removeHighlight(id);
    },
    removeHighlight(id){
      document.querySelectorAll(`mark[data-cid="${id}"]`).forEach(mark => {
        mark.replaceWith(document.createTextNode(mark.textContent));
      });
    },
  }
}
</script>
@endpush

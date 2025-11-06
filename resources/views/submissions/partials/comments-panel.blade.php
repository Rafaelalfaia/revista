@props(['submission','review'=>null])

@php
  $u = auth()->user();
  $isAdmin    = $u->hasRole('Admin');
  $isReviewer = $review && $u->id === $review->reviewer_id;
  $isAuthor   = $u->id === $submission->user_id;

  $canComment = $isAdmin || $isReviewer;
  $canVerify  = $isAdmin || $isReviewer;

  $level  = request('level');
  $status = request('status');

  $commentsQ = $submission->comments()->with(['author:id,name','section:id,title'])->latest();
  if ($level)  $commentsQ->where('level',  $level);
  if ($status) $commentsQ->where('status', $status);
  $comments = $commentsQ->get();

  $totalAll   = $submission->comments()->count();
  $totalOpen  = $submission->comments()->where('status','open')->count();
  $totalClosed= $submission->comments()->where('status','applied')->count();

  $formId = 'comment-form-'.$submission->id;
@endphp

@once
  @push('head')
    <style>
      .cmt-input, .cmt-select, .cmt-textarea{
        width:100%; border:1px solid var(--line); background:var(--panel); color:var(--text);
        border-radius:.75rem; padding:.5rem .75rem;
      }
      .cmt-chip{display:inline-flex;align-items:center;gap:.4rem;border:1px solid var(--line);
        border-radius:.75rem;padding:.2rem .55rem;font-size:.75rem;line-height:1;background:transparent}
      .cmt-chip[data-active="1"]{background:var(--panel-2)}
      .cmt-lv{border-radius:.5rem;padding:.1rem .4rem;font-size:.70rem}
      .cmt-lv[data-lv="must_fix"]{background:rgba(225,29,72,.12);color:var(--text)}
      .cmt-lv[data-lv="should_fix"]{background:rgba(234,179,8,.18);color:var(--text)}
      .cmt-lv[data-lv="nit"]{background:rgba(99,102,241,.14);color:var(--text)}
      .cmt-btn{border:1px solid var(--line);border-radius:.6rem;height:2.25rem;padding:0 .7rem;font-size:.875rem}
      .cmt-btn--brand{background:var(--brand);color:#fff;border-color:transparent}
      .cmt-quote{border-left:4px solid var(--line);padding-left:.5rem;font-style:italic}
      .cmt-item{border:1px solid var(--line);border-radius:.9rem;padding:.75rem;background:var(--panel)}
      .cmt-muted{color:var(--muted)}
    </style>
  @endpush
@endonce

<div class="space-y-4">

  {{-- Header + contadores / filtros rápidos --}}
  <div class="panel-2 border rounded-xl p-3" style="border-color:var(--line)">
    <div class="flex flex-wrap items-center justify-between gap-2">
      <div class="font-semibold">Correções & Comentários</div>
      <div class="flex items-center gap-2 text-xs">
        <span class="cmt-chip {{ $status===''||$status===null?'ring-1':'' }}" data-active="{{ $status===''||$status===null?1:0 }}">
          Total: {{ $totalAll }}
        </span>
        <a class="cmt-chip" href="{{ request()->fullUrlWithQuery(['status'=>'open']) }}" data-active="{{ $status==='open'?1:0 }}">
          Abertos: {{ $totalOpen }}
        </a>
        <a class="cmt-chip" href="{{ request()->fullUrlWithQuery(['status'=>'applied']) }}" data-active="{{ $status==='applied'?1:0 }}">
          Fechados: {{ $totalClosed }}
        </a>
        @if($status || $level)
          <a class="cmt-chip" href="{{ request()->url() }}">Limpar</a>
        @endif
      </div>
    </div>
  </div>

  {{-- Form de criação --}}
  @if($canComment)
    <form id="{{ $formId }}" method="POST" action="{{ route('comments.store', $submission) }}"
          class="panel border rounded-xl p-3" style="border-color:var(--line)">
      @csrf
      <input type="hidden" name="section_id" value="">
      <input type="hidden" name="type" value="suggestion">

      <div class="grid gap-3 md:grid-cols-2">
        <div>
          <label class="text-sm font-medium">Severidade</label>
          <select name="level" class="cmt-select">
            <option value="must_fix">Precisa corrigir</option>
            <option value="should_fix">Recomendado</option>
            <option value="nit">Detalhe</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-medium">Trecho selecionado</label>
          <textarea name="excerpt" rows="2" class="cmt-textarea" placeholder="Selecione no texto à esquerda"></textarea>
        </div>
      </div>

      <div class="mt-2">
        <label class="text-sm font-medium">Texto sugerido / comentário</label>
        <textarea name="suggested_text" rows="3" class="cmt-textarea"
                  placeholder="Explique a correção, ou proponha um texto melhor"></textarea>
        <div class="mt-1 text-xs cmt-muted">Dica: <kbd>Ctrl</kbd> + <kbd>Enter</kbd> envia.</div>
      </div>

      <div class="mt-3 flex items-center justify-end gap-2">
        <button type="submit" class="cmt-btn cmt-btn--brand">Adicionar</button>
      </div>
    </form>
  @endif

  {{-- Filtros finos --}}
  <form method="GET" class="panel-2 border rounded-xl p-3 flex flex-wrap gap-2 items-end" style="border-color:var(--line)">
    <div>
      <label class="text-xs block cmt-muted">Severidade</label>
      <select name="level" class="cmt-select text-sm">
        <option value="">Todas</option>
        <option value="must_fix" @selected(request('level')==='must_fix')>Precisa corrigir</option>
        <option value="should_fix" @selected(request('level')==='should_fix')>Recomendado</option>
        <option value="nit" @selected(request('level')==='nit')>Detalhe</option>
      </select>
    </div>
    <div>
      <label class="text-xs block cmt-muted">Status</label>
      <select name="status" class="cmt-select text-sm">
        <option value="">Todos</option>
        <option value="open"     @selected(request('status')==='open')>Aberto</option>
        <option value="applied"  @selected(request('status')==='applied')>Fechado</option>
        <option value="accepted" @selected(request('status')==='accepted')>Aceito</option>
        <option value="rejected" @selected(request('status')==='rejected')>Rejeitado</option>
      </select>
    </div>
    <button class="cmt-btn">Filtrar</button>
  </form>

  {{-- Lista --}}
  <div class="space-y-3">
    @forelse($comments as $c)
      @php
        $excerptRaw  = $c->excerpt ?? $c->quote ?? '';
        $attrExcerpt = htmlspecialchars($excerptRaw, ENT_QUOTES);
        $display     = $excerptRaw ?: null;
      @endphp

      <div class="cmt-item"
           data-comment
           data-cid="{{ $c->id }}"
           data-sid="{{ $c->section_id }}"
           data-excerpt="{{ $attrExcerpt }}">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <div class="text-xs cmt-muted">
            #{{ $c->id }}
            <span class="cmt-lv" data-lv="{{ $c->level }}">{{ strtoupper($c->level) }}</span>
            • {{ $c->status }}
            @if($c->section) • <em>{{ $c->section->title }}</em>@endif
          </div>
          <div class="text-xs cmt-muted">{{ $c->author->name ?? '—' }}</div>
        </div>

        @if($display)
          <blockquote class="cmt-quote text-sm mt-2">
            “{{ \Illuminate\Support\Str::limit($display, 180) }}”
          </blockquote>
        @endif

        @if($c->suggested_text)
          <div class="text-sm mt-2">{!! nl2br(e($c->suggested_text)) !!}</div>
        @endif

        <div class="flex flex-wrap gap-2 mt-3">
          <button type="button" class="cmt-btn" data-jump
                  data-cid="{{ $c->id }}" data-sid="{{ $c->section_id }}" data-excerpt="{{ $attrExcerpt }}">
            Ir ao trecho
          </button>

          @if($isAuthor && $c->status === 'open')
            <form method="POST" action="{{ route('comments.author_resolved', [$submission,$c]) }}">
              @csrf @method('PATCH')
              <button class="cmt-btn">Autor: marquei como resolvido</button>
            </form>
          @endif

          @if($canVerify)
            @if($c->status === 'open')
              <form method="POST" action="{{ route('comments.verify', [$submission,$c]) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="action" value="accept">
                <button class="cmt-btn cmt-btn--brand">Verificar e fechar</button>
              </form>
            @else
              <form method="POST" action="{{ route('comments.verify', [$submission,$c]) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="action" value="reopen">
                <button class="cmt-btn">Reabrir</button>
              </form>
            @endif
          @endif

          @php $canDelete = $canVerify || $isAdmin; @endphp
          @if($canDelete)
            <form method="POST" action="{{ route('comments.destroy', [$submission,$c]) }}"
                  onsubmit="return confirm('Excluir este comentário?');">
              @csrf @method('DELETE')
              <button class="cmt-btn">Excluir</button>
            </form>
          @endif
        </div>
      </div>
    @empty
      <div class="panel-2 border rounded-xl p-3 text-sm cmt-muted" style="border-color:var(--line)">
        Nenhuma correção/comentário ainda.
      </div>
    @endforelse
  </div>
</div>

<script>
(function(){
  const form = document.getElementById(@json($formId));
  if (form){
    // Preenche section_id + excerpt pela seleção na leitura
    document.addEventListener('mouseup', () => {
      const sel = window.getSelection && window.getSelection();
      const txt = sel ? String(sel).trim() : '';
      if (!txt) return;
      let el = sel.anchorNode;
      while (el && el.nodeType !== 1) el = el.parentNode;
      while (el && !(el.dataset && el.dataset.sectionId)) el = el.parentNode;
      if (el && el.dataset.sectionId) {
        const sidInput = form.querySelector('input[name="section_id"]');
        const exInput  = form.querySelector('textarea[name="excerpt"]');
        if (sidInput) sidInput.value = el.dataset.sectionId;
        if (exInput)  exInput.value  = txt;
      }
    });
    // Ctrl+Enter envia
    form.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        form.requestSubmit();
      }
    });
  }

  // Botão "Ir ao trecho" — tenta destacar/rolar se houver leitura na página
  const esc = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  function markInSection(sectionId, excerpt, cid) {
    if (!excerpt || !sectionId) return null;
    const box = document.querySelector(`[data-content-for="${sectionId}"], .read-section[data-section-id="${sectionId}"] .read-content`);
    if (!box) return null;
    if (box.querySelector(`[data-cid="${cid}"]`)) return box.querySelector(`[data-cid="${cid}"]`);
    const re = new RegExp(esc(excerpt), 'i');
    const html = box.innerHTML;
    if (!re.test(html)) return null;
    const newHtml = html.replace(re, (m) => `<mark class="cmt-lv" style="padding:.08rem .25rem" data-cid="${cid}" id="cm-${cid}">${m}</mark>`);
    box.innerHTML = newHtml;
    return box.querySelector(`#cm-${cid}`);
  }
  document.addEventListener('click', (ev) => {
    const btn = ev.target.closest('[data-jump]');
    if (!btn) return;
    const cid = btn.dataset.cid, sid = btn.dataset.sid, excerpt = btn.dataset.excerpt || '';
    const mark = document.getElementById(`cm-${cid}`) || markInSection(sid, excerpt, cid);
    if (mark) {
      mark.scrollIntoView({behavior:'smooth', block:'center'});
      mark.style.outline = '3px solid rgba(225,29,72,.45)';
      setTimeout(()=>{ mark.style.outline='none'; }, 800);
    } else {
      const sec = document.querySelector(`#sec-${sid}`) || document.querySelector(`.read-section[data-section-id="${sid}"]`);
      if (sec) sec.scrollIntoView({behavior:'smooth', block:'start'});
    }
  });
})();
</script>

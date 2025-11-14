@props(['submission','comments'=>null,'isAuthor'=>false,'canVerify'=>false])

@php
  use Illuminate\Support\Facades\Schema;

  $u = auth()->user();
  $level  = request('level');
  $status = request('status');

  if ($isAuthor && ($status === null || $status === '')) {
    $status = 'open';
  }

  if (!$comments) {
    $q = $submission->comments()->with(['user:id,name','section:id,title'])->latest();
    if ($level)  $q->where('level',$level);
    if ($status) $q->where('status',$status);
    if (Schema::hasColumn('submission_comments','audience') && $isAuthor) {
      $q->whereIn('audience',['author','both']);
    }
    $comments = $q->get();
  }

  $totalAll    = $submission->comments()->count();
  $totalOpen   = $submission->comments()->where('status','open')->count();
  $totalClosed = $submission->comments()->where('status','applied')->count();

  $lvLabel = fn($lv)=>(['must_fix'=>'Precisa corrigir','should_fix'=>'Recomendado','nit'=>'Detalhe'])[$lv] ?? strtoupper($lv);
  $stLabel = fn($st)=>(['open'=>'Aberto','applied'=>'Fechado','accepted'=>'Aceito','rejected'=>'Rejeitado'])[$st] ?? $st;

  $formId = 'comment-form-'.$submission->id;
@endphp

@once
  @push('head')
    <style>
      .cmt-input,.cmt-select,.cmt-textarea{width:100%;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.75rem;padding:.5rem .75rem}
      .cmt-chip{display:inline-flex;align-items:center;gap:.4rem;border:1px solid var(--line);border-radius:.75rem;padding:.2rem .55rem;font-size:.75rem;line-height:1;background:transparent}
      .cmt-chip[data-active="1"]{background:var(--panel-2)}
      .cmt-lv{border-radius:.5rem;padding:.1rem .4rem;font-size:.70rem}
      .cmt-btn{border:1px solid var(--line);border-radius:.6rem;height:2.25rem;padding:0 .7rem;font-size:.875rem}
      .cmt-btn--brand{background:var(--brand);color:#fff;border-color:transparent}
      .cmt-quote{border-left:4px solid var(--line);padding-left:.5rem;font-style:italic}
      .cmt-item{border:1px solid var(--line);border-radius:.9rem;padding:.75rem;background:var(--panel)}
      .cmt-muted{color:var(--muted)}
      .cm-badge-ok{background:rgba(16,185,129,.12);color:#16a34a;border:1px solid rgba(16,185,129,.35)}
      .cm-badge-pend{background:rgba(225,29,72,.12);color:#dc2626;border:1px solid rgba(225,29,72,.35)}
    </style>
  @endpush
@endonce

<div class="space-y-4">
  <div class="panel-2 border rounded-xl p-3" style="border-color:var(--line)">
    <div class="flex flex-wrap items-center justify-between gap-2">
      <div class="font-semibold">Correções & Comentários</div>
      <div class="flex items-center gap-2 text-xs">
        <span class="cmt-chip" data-active="{{ $status===''||$status===null?1:0 }}">Total: {{ $totalAll }}</span>
        <a class="cmt-chip" href="{{ request()->fullUrlWithQuery(['status'=>'open']) }}" data-active="{{ $status==='open'?1:0 }}">Abertos: {{ $totalOpen }}</a>
        <a class="cmt-chip" href="{{ request()->fullUrlWithQuery(['status'=>'applied']) }}" data-active="{{ $status==='applied'?1:0 }}">Fechados: {{ $totalClosed }}</a>
        @if($status || $level)
          <a class="cmt-chip" href="{{ request()->url() }}">Limpar</a>
        @endif
      </div>
    </div>
  </div>

  @if($u && $u->hasAnyRole(['Admin','Revisor']))
    <form id="{{ $formId }}" method="POST" action="{{ route('comments.store', $submission) }}" class="panel border rounded-xl p-3" style="border-color:var(--line)">
      @csrf
      <input type="hidden" name="section_id" value="">
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
        <textarea name="suggested_text" rows="3" class="cmt-textarea" placeholder="Escreva a sugestão do texto a substituir ou o comentário"></textarea>
        <div class="mt-1 text-xs cmt-muted">Dica: Ctrl + Enter envia.</div>
      </div>
      <div class="mt-3 flex items-center justify-end gap-2">
        <button type="submit" class="cmt-btn cmt-btn--brand">Adicionar</button>
      </div>
    </form>
  @endif

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

  <div class="space-y-3">
    @forelse($comments as $c)
      @php
        $excerptRaw  = $c->excerpt ?? '';
        $attrExcerpt = htmlspecialchars($excerptRaw, ENT_QUOTES);
        $display     = $excerptRaw ?: null;

        $hasClosedBy    = Schema::hasColumn('submission_comments','closed_by');
        $closedByAuthor = ($c->status !== 'open') && ($hasClosedBy ? (($c->closed_by ?? null) === 'author') : ($c->status === 'applied'));
        $statusText     = $closedByAuthor ? 'Status: Corrigido pelo Autor' : 'Status: Não Corrigido';
        $statusClass    = $closedByAuthor ? 'cm-badge-ok' : 'cm-badge-pend';

        $lvBg = match($c->level){
          'should_fix' => 'rgba(234,179,8,.18)',
          'nit'        => 'rgba(99,102,241,.14)',
          default      => 'rgba(225,29,72,.12)'
        };
      @endphp

      <div class="cmt-item" data-comment data-cid="{{ $c->id }}" data-sid="{{ $c->section_id }}" data-excerpt="{{ $attrExcerpt }}">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <div class="text-xs cmt-muted">
            <span class="cmt-lv" style="background:{{ $lvBg }}">{{ $lvLabel($c->level) }}</span>
            • {{ $stLabel($c->status) }}
            @if($c->section) • <em>{{ $c->section->title }}</em>@endif
          </div>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $statusClass }}">{{ $statusText }}</span>
        </div>

        @if($display)
          <blockquote class="cmt-quote text-sm mt-2">“{{ \Illuminate\Support\Str::limit($display, 180) }}”</blockquote>
        @endif

        @if($c->suggested_text)
          <div class="text-sm mt-2">{!! nl2br(e($c->suggested_text)) !!}</div>
        @elseif($c->body)
          <div class="text-sm mt-2">{!! nl2br(e($c->body)) !!}</div>
        @endif

        <div class="flex flex-wrap gap-2 mt-3">
          <button type="button" class="cmt-btn" data-jump data-cid="{{ $c->id }}" data-sid="{{ $c->section_id }}" data-excerpt="{{ $attrExcerpt }}">Ir ao trecho</button>

          @if($isAuthor && $c->status === 'open')
            <form method="POST" action="{{ route('comments.author_resolved', [$submission,$c]) }}">
              @csrf @method('PATCH')
              <button class="cmt-btn">Marcar como resolvido</button>
            </form>
          @endif

          @if($canVerify)
            @if($c->status === 'open')
              <form method="POST" action="{{ route('comments.verify', [$submission,$c]) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="action" value="accept">
                <button class="cmt-btn">Verificar e fechar</button>
              </form>
            @else
              <form method="POST" action="{{ route('comments.verify', [$submission,$c]) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="action" value="reopen">
                <button class="cmt-btn">Reabrir</button>
              </form>
            @endif
          @endif

          @if($u && $u->hasAnyRole(['Admin','Revisor']))
            <form method="POST" action="{{ route('comments.destroy', [$submission,$c]) }}" onsubmit="return confirm('Excluir este comentário?');">
              @csrf @method('DELETE')
              <button class="cmt-btn">Excluir</button>
            </form>
          @endif
        </div>
      </div>
    @empty
      <div class="panel-2 border rounded-xl p-3 text-sm cmt-muted" style="border-color:var(--line)">Nenhuma correção/comentário ainda.</div>
    @endforelse
  </div>
</div>

<script>
(function(){
  const form = document.getElementById(@json($formId));
  if (form){
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
    form.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') form.requestSubmit();
    });
  }
  const esc = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  function markInSection(sectionId, excerpt, cid) {
    if (!excerpt || !sectionId) return null;
    const box = document.querySelector(`[data-content-for="${sectionId}"], .read-section[data-section-id="${sectionId}"] .read-content`);
    if (!box) return null;
    if (box.querySelector(`[data-cid="${cid}"]`)) return box.querySelector(`[data-cid="${cid}"]`);
    const words = excerpt.trim().split(/\s+/).map(esc).join('(?:\\s|<[^>]+>)*');
    const re = new RegExp(words, 'i');
    const html = box.innerHTML;
    if (!re.test(html)) return null;
    const newHtml = html.replace(re, (m) => `<mark class="cmt-lv" style="padding:.08rem .25rem" data-cid="${cid}" id="cm-${cid}">${m}</mark>`);
    box.innerHTML = newHtml;
    return box.querySelector(`#cm-${cid}`);
  }
  document.addEventListener('click', (ev) => {
    const jump = ev.target.closest('[data-jump]');
    if (jump) {
      const cid = jump.dataset.cid, sid = jump.dataset.sid, excerpt = jump.dataset.excerpt || '';
      const mark = document.getElementById(`cm-${cid}`) || markInSection(sid, excerpt, cid);
      if (mark) { mark.scrollIntoView({behavior:'smooth', block:'center'}); mark.style.outline = '3px solid rgba(225,29,72,.45)'; setTimeout(()=>{ mark.style.outline='none'; }, 800); }
      else {
        const sec = document.querySelector(`#sec-${sid}`) || document.querySelector(`.read-section[data-section-id="${sid}"]`);
        if (sec) sec.scrollIntoView({behavior:'smooth', block:'start'});
      }
      return;
    }
    const newc = ev.target.closest('[data-newcomment]');
    if (newc && form) {
      const sid = newc.dataset.sid || '';
      const sidInput = form.querySelector('input[name="section_id"]');
      if (sidInput) sidInput.value = sid;
      const sug = form.querySelector('textarea[name="suggested_text"]');
      if (sug) sug.focus();
    }
  });
})();
</script>

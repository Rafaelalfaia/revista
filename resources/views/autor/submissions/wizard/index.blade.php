@extends('console.layout-author')
@section('title','Seções do Projeto')
@section('page.title','Seções do Projeto')

@push('head')
<style>
  .wizard-shell{display:flex;flex-direction:column;gap:1rem}
  .wizard-flash{border-radius:.9rem;border:1px solid var(--line);background:var(--panel);padding:.6rem .8rem;font-size:.8rem}
  .wizard-flash-ok{border-color:rgba(16,185,129,.4);background:rgba(16,185,129,.08);color:#047857}
  .wizard-flash-err{border-color:rgba(248,113,113,.5);background:rgba(254,226,226,1);color:#b91c1c}
  .wizard-flash-warn{border-color:rgba(245,158,11,.4);background:rgba(245,158,11,.08);color:#854d0e}

  .wizard-grid{display:grid;gap:1rem}
  @media(min-width:1024px){.wizard-grid{grid-template-columns:minmax(0,2.1fr) minmax(0,1.1fr)}}

  .wizard-card{border-radius:1.3rem;border:1px solid var(--line);background:radial-gradient(circle at top left,rgba(251,113,133,.14),transparent 55%),radial-gradient(circle at top right,rgba(59,130,246,.18),transparent 55%),var(--panel);overflow:hidden}
  .wizard-card-simple{border-radius:1.1rem;border:1px solid var(--line);background:var(--panel);overflow:hidden}

  .wizard-card-head{padding:.9rem 1rem;border-bottom:1px solid rgba(148,163,184,.4);display:flex;flex-direction:column;align-items:flex-start;justify-content:space-between;gap:.6rem}
  @media(min-width:640px){.wizard-card-head{flex-direction:row;align-items:flex-start;gap:.75rem}}

  .wizard-card-title{font-size:1rem;font-weight:700}
  .wizard-card-sub{font-size:.78rem;color:var(--muted);margin-top:.1rem}
  .wizard-card-meta{display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.35rem;font-size:.75rem;color:var(--muted)}
  .wizard-pill{border-radius:999px;padding:.1rem .6rem;border:1px solid var(--line);background:var(--soft);display:inline-flex;align-items:center;gap:.3rem;max-width:100%;white-space:nowrap}
  .wizard-dot{width:.38rem;height:.38rem;border-radius:999px;background:var(--brand)}
  .wizard-card-body{padding:.9rem 1rem 1.1rem;display:flex;flex-direction:column;gap:.75rem}

  .wizard-locked{opacity:.75}

  .wizard-sections-list{display:flex;flex-direction:column;gap:.55rem}
  .wizard-section-item{border-radius:1.1rem;border:1px solid var(--line);padding:.6rem .75rem;display:flex;flex-direction:column;align-items:flex-start;gap:.4rem;background:var(--panel);transition:border-color .15s,background .15s,transform .1s}
  .wizard-section-item:hover{border-color:var(--brand);background:rgba(251,113,133,.05);transform:translateY(-1px)}
  @media(min-width:640px){
    .wizard-section-item{flex-direction:row;align-items:center;justify-content:space-between}
  }

  .wizard-section-main{min-width:0;width:100%}
  .wizard-section-title-line{display:flex;align-items:flex-start;gap:.4rem;min-width:0}
  .wizard-section-index{display:inline-flex;width:1.4rem;height:1.4rem;border-radius:999px;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;flex-shrink:0}
  .wizard-section-title{font-size:.86rem;font-weight:600;line-height:1.3;word-break:break-word}
  .wizard-section-title .muted{font-size:.8rem}
  .wizard-section-badges{margin-top:.2rem;font-size:.7rem;display:flex;flex-wrap:wrap;gap:.25rem}
  .wizard-section-state{font-size:.72rem;color:var(--muted);white-space:nowrap}

  .wizard-actions-row{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.6rem}

  .btn{border-radius:.85rem;padding:.55rem 1.05rem;font-weight:600;font-size:.82rem;display:inline-flex;align-items:center;gap:.3rem}
  .btn-neutral{border:1px solid var(--line);background:var(--panel);color:var(--text)}
  .btn-neutral:hover{background:rgba(148,163,184,.12)}
  .btn-brand{background:var(--brand);color:#fff;border:none}
  .btn-brand[disabled]{opacity:.6;cursor:not-allowed}
  .btn-brand:hover:not([disabled]){filter:brightness(1.02)}

  .checklist{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:.45rem}
  .checklist li{display:flex;align-items:flex-start;gap:.45rem;font-size:.8rem}
  .ok{color:#16a34a}
  .no{color:#dc2626}

  .trv-input,.trv-select,.trv-textarea{width:100%;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:.85rem;padding:.55rem .8rem;font-size:.85rem;transition:box-shadow .15s,border-color .15s,background .15s,color .15s}
  .trv-input:focus,.trv-select:focus,.trv-textarea:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(225,29,72,.22)}
  .trv-label{display:block;font-size:.85rem;font-weight:600;margin-bottom:.25rem}
  .trv-help{font-size:.75rem;color:var(--muted)}

  .wizard-meta-footer{display:flex;justify-content:flex-end;margin-top:.75rem}
  .wizard-category-list{max-height:16rem;overflow:auto;border-radius:.95rem;border:1px solid var(--line)}
  .wizard-category-item{display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.4rem .7rem;font-size:.83rem}
  .wizard-category-item:nth-child(odd){background:rgba(15,23,42,.012)}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Facades\Schema;

  $locked = method_exists($sub,'isLocked') ? $sub->isLocked() : in_array($sub->status, ['aceito','rejeitado'], true);

  $hasTitle    = filled($sub->title);
  $hasAbstract = mb_strlen((string)($sub->abstract ?? '')) >= 50;

  $sectionStates = collect($sections ?? [])->map(function($sec){
    $plain = trim(preg_replace('/\s+/',' ', strip_tags((string)($sec->content ?? ''))));
    return mb_strlen($plain) >= 30;
  });
  $allSectionsFilled  = $sectionStates->every(fn($v)=>$v===true);
  $completedSections  = $sectionStates->filter(fn($v)=>$v===true)->count();
  $totalSections      = $sectionStates->count();

  $hasCategory = !empty($primaryCategoryId);
  $canSubmit   = $hasTitle && $hasAbstract && $allSectionsFilled && $hasCategory;

  $cQuery = $sub->comments()->select('id','section_id','level','status','audience');
  $cQuery->where('status','open');
  if (Schema::hasColumn('submission_comments','audience')) {
      $cQuery->whereIn('audience', ['author','both']);
  }
  $openComments = $cQuery->get()->groupBy('section_id');
  $openTotal    = $openComments->flatten()->count();

  $alreadySubmitted   = !is_null($sub->submitted_at);
  $hasOpenCorrections = $openTotal > 0;
  $showSubmitInitial  = !$alreadySubmitted && !$locked;
  $showUpdateButton   = $alreadySubmitted && !$hasOpenCorrections && !$locked;

  $badge = function(int $n, string $label, string $color) {
    if ($n <= 0) return '';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold" style="background:'.$color.'15;color:'.$color.';border:1px solid '.$color.'33;margin-right:.2rem">'.e($label).' '.$n.'</span>';
  };
@endphp

<div class="wizard-shell">
  @if (session('ok'))
    <div class="wizard-flash wizard-flash-ok">{{ session('ok') }}</div>
  @endif
  @if (session('error'))
    <div class="wizard-flash wizard-flash-err">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="wizard-flash wizard-flash-err">
      <ul class="list-disc ml-4">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if($locked)
    <div class="wizard-flash wizard-flash-warn">
      Projeto finalizado. Alterações não são permitidas; você pode apenas visualizar as seções.
    </div>
  @endif

  <div class="wizard-grid">
    <div class="space-y-4">
      <div class="wizard-card {{ $locked ? 'wizard-locked' : '' }}">
        <div class="wizard-card-head">
          <div class="min-w-0">
            <div class="wizard-card-title truncate" title="{{ $sub->title }}">
              {{ $sub->title ?: 'Projeto sem título' }}
            </div>
            <div class="wizard-card-sub">
              Organize e preencha cada seção do manuscrito antes de enviar para análise.
            </div>
            <div class="wizard-card-meta">
              <span class="wizard-pill">
                <span class="wizard-dot"></span>
                <span>{{ $completedSections }} de {{ $totalSections }} seções preenchidas</span>
              </span>
              @if($openTotal > 0)
                <span class="wizard-pill" style="border-color:rgba(248,113,113,.4);background:rgba(248,113,113,.06);color:#b91c1c">
                  Pendências: {{ $openTotal }}
                </span>
              @endif
              <span class="wizard-pill">
                <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded-full" style="background:rgba(15,23,42,.08)">
                  {{ $sub->type_label ?? $sub->tipo_trabalho }}
                </span>
              </span>
            </div>
          </div>
        </div>

        <div class="wizard-card-body">
          <div class="wizard-sections-list">
            @foreach ($sections as $sec)
              @php
                $plain = trim(preg_replace('/\s+/',' ', strip_tags((string)($sec->content ?? ''))));
                $filled = mb_strlen($plain) >= 30;
                $borderColor = $filled ? 'var(--brand)' : 'var(--line)';
                $circleBg = $filled ? 'var(--brand)' : 'var(--chip)';
                $circleColor = $filled ? '#fff' : 'var(--text)';
                $list   = $openComments->get($sec->id) ?? collect();
                $mf     = $list->where('level','must_fix')->count();
                $sf     = $list->where('level','should_fix')->count();
                $nit    = $list->where('level','nit')->count();
              @endphp
              <a href="{{ route('autor.submissions.section.edit', [$sub, $sec]) }}"
                 class="wizard-section-item"
                 style="border-color:{{ $borderColor }};">
                <div class="wizard-section-main">
                  <div class="wizard-section-title-line">
                    <span class="wizard-section-index"
                          style="background:{{ $circleBg }};color:{{ $circleColor }};">
                      {{ $loop->iteration }}
                    </span>
                    <span class="wizard-section-title">
                      @if($sec->numbering)
                        <span class="muted mr-1">{{ $sec->numbering }}</span>
                      @endif
                      {{ $sec->title }}
                    </span>
                  </div>
                  @if($mf + $sf + $nit > 0)
                    <div class="wizard-section-badges">
                      {!! $badge($mf, 'Correções obrigatórias', '#dc2626') !!}
                      {!! $badge($sf, 'Ajustes sugeridos', '#d97706') !!}
                      {!! $badge($nit,'Observações', '#6b7280') !!}
                    </div>
                  @endif
                </div>
                <div class="wizard-section-state">
                  {{ $filled ? 'Preenchida' : 'Pendente' }}
                </div>
              </a>
            @endforeach
          </div>

          @if ($first)
            <div class="wizard-actions-row">
              <a href="{{ route('autor.submissions.section.edit', [$sub, $first]) }}"
                 class="btn btn-neutral">
                {{ $locked ? 'Ver seções' : 'Começar / Continuar' }}
              </a>
            </div>
          @endif
        </div>
      </div>

      <div class="wizard-card-simple {{ $locked ? 'wizard-locked' : '' }}">
        <div class="wizard-card-head">
          <div>
            <div class="wizard-card-title">Enviar para análise</div>
            <div class="wizard-card-sub">
              Confira os requisitos mínimos antes de enviar ou atualizar o projeto em revisão.
            </div>
          </div>
        </div>
        <div class="wizard-card-body">
          <ul class="checklist mb-2">
            <li>
              @if($hasTitle)
                <svg width="18" height="18" class="ok" viewBox="0 0 24 24"><path fill="currentColor" d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
              @else
                <svg width="18" height="18" class="no" viewBox="0 0 24 24"><path fill="currentColor" d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.71 2.89 18.3 9.17 12 2.89 5.71 4.3 4.29l6.29 6.3 6.29-6.3z"/></svg>
              @endif
              <span>Título preenchido</span>
            </li>
            <li>
              @if($hasAbstract)
                <svg width="18" height="18" class="ok" viewBox="0 0 24 24"><path fill="currentColor" d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
              @else
                <svg width="18" height="18" class="no" viewBox="0 0 24 24"><path fill="currentColor" d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.71 2.89 18.3 9.17 12 2.89 5.71 4.3 4.29l6.29 6.3 6.29-6.3z"/></svg>
              @endif
              <span>Resumo com pelo menos 50 caracteres</span>
            </li>
            <li>
              @if($allSectionsFilled)
                <svg width="18" height="18" class="ok" viewBox="0 0 24 24"><path fill="currentColor" d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
              @else
                <svg width="18" height="18" class="no" viewBox="0 0 24 24"><path fill="currentColor" d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.71 2.89 18.3 9.17 12 2.89 5.71 4.3 4.29l6.29 6.3 6.29-6.3z"/></svg>
              @endif
              <span>Todas as seções com conteúdo mínimo</span>
            </li>
            <li>
              @if($hasCategory)
                <svg width="18" height="18" class="ok" viewBox="0 0 24 24"><path fill="currentColor" d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
              @else
                <svg width="18" height="18" class="no" viewBox="0 0 24 24"><path fill="currentColor" d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.71 2.89 18.3 9.17 12 2.89 5.71 4.3 4.29l6.29 6.3 6.29-6.3z"/></svg>
              @endif
              <span>Categoria principal selecionada</span>
            </li>
          </ul>

          @if ($showSubmitInitial)
            <form method="POST"
                  action="{{ route('autor.submissions.submit',$sub) }}"
                  onsubmit="return {{ $canSubmit ? 'confirm(\'Confirmar envio para análise?\')' : 'false' }};">
              @csrf
              <button class="btn btn-brand" @if(!$canSubmit) disabled @endif>Enviar para análise</button>
            </form>
          @elseif ($showUpdateButton)
            <form method="POST"
                  action="{{ route('autor.submissions.updateAfterSubmit',$sub) }}"
                  onsubmit="return {{ $canSubmit ? 'confirm(\'Atualizar o projeto para a revisão?\')' : 'false' }};">
              @csrf
              @method('PATCH')
              <button class="btn btn-brand" @if(!$canSubmit) disabled @endif>Atualizar projeto em revisão</button>
            </form>
          @else
            @if($locked)
              <button class="btn btn-brand" disabled>Envio desabilitado</button>
            @else
              <div class="text-sm mb-2" style="color:#dc2626">
                Há correções pendentes direcionadas a você. Resolva-as para poder reenviar ou atualizar o projeto.
              </div>
              <button class="btn btn-brand" disabled>Envio desabilitado</button>
            @endif
          @endif
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="wizard-card-simple {{ $locked ? 'wizard-locked' : '' }}">
        <div class="wizard-card-head">
          <div>
            <div class="wizard-card-title">Metadados</div>
            <div class="wizard-card-sub">Edite título, resumo, palavras-chave e tipo de manuscrito.</div>
          </div>
        </div>
        <div class="wizard-card-body">
          <fieldset @disabled($locked)>
            <form method="POST" action="{{ route('autor.submissions.metadata.update', $sub) }}" class="space-y-3">
              @csrf
              <div>
                <label class="trv-label">Título</label>
                <input name="title" value="{{ old('title',$sub->title) }}" class="trv-input">
              </div>
              <div>
                <label class="trv-label">Resumo</label>
                <textarea name="abstract" rows="4" class="trv-textarea">{{ old('abstract',$sub->abstract) }}</textarea>
              </div>
              <div>
                <label class="trv-label">Palavras-chave</label>
                <input name="keywords" value="{{ old('keywords', implode(', ', (array)$sub->keywords)) }}" class="trv-input" placeholder="Separe por vírgula">
                <p class="trv-help mt-1">Exemplo: educação, avaliação, tecnologia, ensino.</p>
              </div>
              <div>
                @php $types=\App\Models\Submission::TYPE_LABELS ?? []; @endphp
                <label class="trv-label">Tipo de manuscrito</label>
                <select name="tipo_trabalho" class="trv-select">
                  @forelse($types as $k=>$label)
                    <option value="{{ $k }}" @selected($sub->tipo_trabalho===$k)>{{ $label }}</option>
                  @empty
                    <option value="{{ $sub->tipo_trabalho }}" selected>{{ $sub->tipo_trabalho }}</option>
                  @endforelse
                </select>
                <p class="trv-help mt-1">Ao mudar o tipo, as seções padrão podem ser ajustadas para o novo formato.</p>
              </div>
              <div class="wizard-meta-footer">
                <button class="btn btn-neutral">Salvar metadados</button>
              </div>
            </form>
          </fieldset>
        </div>
      </div>

      <div class="wizard-card-simple {{ $locked ? 'wizard-locked' : '' }}">
        <div class="wizard-card-head">
          <div>
            <div class="wizard-card-title">Categoria principal</div>
            <div class="wizard-card-sub">Escolha a área principal do manuscrito para facilitar a escolha de revisores.</div>
          </div>
        </div>
        <div class="wizard-card-body">
          <fieldset @disabled($locked)>
            <form method="POST" action="{{ route('autor.submissions.metadata.update', $sub) }}" class="space-y-3">
              @csrf
              <input type="hidden" name="context" value="categories">
              @php $oldCategoryId = old('category_id', $primaryCategoryId ?? null); @endphp

              @if(isset($allCats) && $allCats->count())
                <div class="wizard-category-list">
                  <ul>
                    @foreach ($allCats as $c)
                      <li class="wizard-category-item">
                        <label class="flex items-center gap-2 w-full cursor-pointer">
                          <input type="radio"
                                 name="category_id"
                                 value="{{ $c->id }}"
                                 @checked($oldCategoryId == $c->id)
                                 required>
                          <span>{{ $c->name }}</span>
                        </label>
                      </li>
                    @endforeach
                  </ul>
                </div>
                @error('category_id')
                  <div class="mt-2 text-xs" style="color:#dc2626">{{ $message }}</div>
                @enderror
                <p class="trv-help mt-1">A categoria é obrigatória para enviar o projeto à avaliação.</p>
                <div class="wizard-meta-footer">
                  <button class="btn btn-neutral">Salvar categoria</button>
                </div>
              @else
                <p class="trv-help">Nenhuma categoria disponível. Solicite ao Admin ou Coordenador o cadastro de áreas.</p>
              @endif
            </form>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- resources/views/admin/submissions/show.blade.php --}}
@extends('console.layout')

@section('title','Submissão #'.$submission->id.' · Admin')
@section('page.title','Submissão · #'.$submission->id)

@section('content')
  {{-- Alerts --}}
  @if (session('ok'))
    <div class="mb-3 rounded-lg border px-3 py-2 text-sm" style="border-color:var(--line)">{{ session('ok') }}</div>
  @endif
  @if (session('error'))
    <div class="mb-3 rounded-lg border px-3 py-2 text-sm" style="border-color:var(--line)">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="mb-3 rounded-lg border px-3 py-2 text-sm" style="border-color:var(--line)">
      @foreach ($errors->all() as $e)
        <div>{{ $e }}</div>
      @endforeach
    </div>
  @endif

  <a href="{{ route('admin.submissions.index') }}" class="text-sm hover:underline muted">← voltar</a>

  <div class="mt-3 grid lg:grid-cols-3 gap-4">

    {{-- ======== COLUNA PRINCIPAL ======== --}}
    <div class="lg:col-span-2 space-y-4">

      {{-- Card: Cabeçalho + Status --}}
      <div class="rounded-xl panel border p-4">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="text-xs muted">Título</div>
            <h2 class="mt-1 text-lg font-semibold leading-snug break-words">
              {{ $submission->title ?? '—' }}
            </h2>
            <div class="mt-1 text-xs muted break-all">Slug: {{ $submission->slug ?? '—' }}</div>
          </div>
          <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs chip">
            {{ str_replace('_',' ',$submission->status) }}
          </span>
        </div>




        {{-- Metadados principais --}}
        <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
          <div>
            <div class="muted text-xs">Autor</div>
            <div>
              @if($submission->author)
                <a href="{{ route('admin.submissions.index', ['author_id' => $submission->author->id]) }}" class="hover:underline">
                  {{ $submission->author->name }}
                </a>
              @else — @endif
            </div>
          </div>
          <div>
            <div class="muted text-xs">Idioma</div>
            <div>{{ $submission->language ?? '—' }}</div>
          </div>
          <div>
            <div class="muted text-xs">Tipo de trabalho</div>
            <div>{{ $submission->tipo_trabalho ?? '—' }}</div>
          </div>
          <div>
            <div class="muted text-xs">Palavras-chave</div>
            <div class="mt-1">
              @forelse(($submission->keywords ?? []) as $k)
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs chip mr-2">{{ $k }}</span>
              @empty — @endforelse
            </div>
          </div>
        </div>


        {{-- Datas relevantes --}}
        <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
          <div>
            <div class="muted text-xs">Criada</div>
            <div>{{ $submission->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
          </div>
          <div>
            <div class="muted text-xs">Submetida</div>
            <div>{{ $submission->submitted_at?->format('d/m/Y H:i') ?? '—' }}</div>
          </div>
          <div>
            <div class="muted text-xs">Triagem</div>
            <div>{{ $submission->triaged_at?->format('d/m/Y H:i') ?? '—' }}</div>
          </div>
          <div>
            <div class="muted text-xs">Aceita</div>
            <div>{{ $submission->accepted_at?->format('d/m/Y H:i') ?? '—' }}</div>
          </div>
        </div>
      </div>

      {{-- Card: Categorias --}}
      <div class="rounded-xl panel border p-4">
        <div class="font-medium">Categorias</div>
        <div class="mt-2 text-sm">
          @php
            // carrega categorias se não carregou
            $cats = $submission->relationLoaded('categories') ? $submission->categories : $submission->categories()->get();
          @endphp
          @if ($cats && $cats->count())
            <div class="flex flex-wrap gap-2">
              @foreach($cats as $c)
                @php $isPrimary = (bool)($c->pivot->is_primary ?? false); @endphp
                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs chip">
                  {{ $c->name }}
                  @if($isPrimary)
                    <svg width="12" height="12" viewBox="0 0 24 24" class="muted">
                      <path fill="currentColor" d="M9.5 16.5L4 11l1.4-1.4l4.1 4.1L18.6 4.6L20 6z"/>
                    </svg>
                  @endif
                </span>
              @endforeach
            </div>
          @else
            <span class="muted">—</span>
          @endif
        </div>
      </div>

      {{-- Card: Resumo --}}
      <div class="rounded-xl panel border p-4">
        <div class="muted text-xs">Resumo</div>
        <p class="mt-1 text-sm whitespace-pre-line">{{ $submission->abstract ?: '—' }}</p>
      </div>

      {{-- Card: Arquivos --}}
      <div class="rounded-xl panel border p-4">
        <div class="font-medium">Arquivos</div>
        <div class="mt-2 overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left muted">
              <tr class="border-b" style="border-color:var(--line)">
                <th class="py-2 px-2">Tipo</th>
                <th class="py-2 px-2">Nome</th>
                <th class="py-2 px-2">Tamanho</th>
                <th class="py-2 px-2">Enviado</th>
                <th class="py-2 px-2">Ação</th>
              </tr>
            </thead>
            <tbody>
              @forelse($submission->files as $f)
                <tr class="border-b last:border-0" style="border-color:var(--line)">
                  <td class="py-2 px-2">{{ $f->role }}</td>
                  <td class="py-2 px-2 break-all">{{ $f->original_name }}</td>
                  <td class="py-2 px-2">
                    {{ $f->size ? number_format($f->size/1024,1,',','.') : '—' }} KB
                  </td>
                  <td class="py-2 px-2">{{ $f->created_at?->format('d/m/Y H:i') }}</td>
                  <td class="py-2 px-2">
                    @php
                      $disk = $f->disk ?? 'public';
                      $path = $f->path ?? $f->file_path ?? null;
                      $url  = $path ? Storage::disk($disk)->url($path) : null;
                    @endphp
                    @if($url)
                      <a href="{{ $url }}" target="_blank" class="hover:underline muted">abrir</a>
                    @else
                      <span class="muted">—</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td class="py-6 px-2 muted" colspan="5">Sem arquivos.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ======== COLUNA LATERAL: AÇÕES ======== --}}
    <div class="rounded-xl panel border p-4" x-data="submissionActions()">
    <div class="font-medium">Ações editoriais</div>

     <a href="{{ route('admin.submissions.read', $submission) }}"
        class="inline-flex items-center gap-2 rounded-lg px-3 h-9 text-sm text-white"
        style="background:var(--brand)">
        Ler
        </a>

    {{-- form "invisível" para CSRF; os botões usam formaction/formmethod --}}
    <form id="transition-form" method="POST" action="{{ route('admin.submissions.transition',$submission) }}" class="hidden">
        @csrf
    </form>

    <div class="mt-3 space-y-2">
        <textarea name="message" x-ref="message" rows="3"
                placeholder="Mensagem ao autor (opcional)"
                class="w-full rounded-lg border px-3 py-2 text-sm bg-transparent"
                style="border-color:var(--line)"></textarea>

        <div class="grid sm:grid-cols-2 gap-2">
        <button type="submit" name="action" value="desk_reject"
                form="transition-form"
                formaction="{{ route('admin.submissions.transition',$submission) }}"
                formmethod="POST"
                @click="confirmAction($event,'Aplicar desk reject?')"
                class="rounded-lg px-3 py-2 text-sm border panel">
            Desk reject
        </button>

        <button type="submit" name="action" value="request_fixes"
                form="transition-form"
                formaction="{{ route('admin.submissions.transition',$submission) }}"
                formmethod="POST"
                @click="ensureMessage($event,'Deseja solicitar correções? Adicione uma orientação ao autor.')"
                class="rounded-lg px-3 py-2 text-sm border panel">
            Solicitar correções
        </button>

        <button type="submit" name="action" value="send_to_review"
                form="transition-form"
                formaction="{{ route('admin.submissions.transition',$submission) }}"
                formmethod="POST"
                @click="confirmAction($event,'Enviar à etapa de revisão?')"
                class="rounded-lg px-3 py-2 text-sm text-white brand">
            Enviar à revisão
        </button>

        <button type="submit" name="action" value="accept"
                form="transition-form"
                formaction="{{ route('admin.submissions.transition',$submission) }}"
                formmethod="POST"
                @click="confirmAction($event,'Aceitar esta submissão?')"
                class="rounded-lg px-3 py-2 text-sm border panel">
            Aceitar
        </button>

        <button type="submit" name="action" value="reject"
                form="transition-form"
                formaction="{{ route('admin.submissions.transition',$submission) }}"
                formmethod="POST"
                @click="confirmAction($event,'Rejeitar esta submissão?')"
                class="rounded-lg px-3 py-2 text-sm border panel">
            Rejeitar
        </button>
        </div>
    </div>
    </div>

<script>
  function submissionActions(){
    return {
      confirmAction(e, msg){
        if(!confirm(msg)) e.preventDefault();
      },
      ensureMessage(e, msg){
        const t = (this.$refs.message?.value || '').trim();
        if(t.length < 5){
          if(!confirm(msg + '\n\nContinuar mesmo sem mensagem?')) e.preventDefault();
        } else {
          // injeta a mensagem no form invisível
          const form = document.getElementById('transition-form');
          let ta = form.querySelector('textarea[name="message"]');
          if(!ta){ ta = document.createElement('textarea'); ta.name = 'message'; ta.className='hidden'; form.appendChild(ta); }
          ta.value = t;
        }
      }
    }
  }
</script>

@endsection

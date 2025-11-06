@extends('console.layout')
@section('title','Triagem · #'.$submission->id)
@section('page.title','Triagem · #'.$submission->id)

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
  @if (session('ok'))
    <div class="mb-3 rounded-lg border px-3 py-2 text-sm" style="border-color:var(--line)">
      {{ session('ok') }}
    </div>
  @endif

  <a href="{{ route('admin.triage.index') }}" class="text-sm hover:underline muted">← voltar</a>

  <div class="mt-3 grid lg:grid-cols-3 gap-4">
    {{-- Metadados e arquivos --}}
    <div class="lg:col-span-2 rounded-xl panel border p-4">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs muted">Título</div>
          <h2 class="text-lg font-semibold">{{ $submission->title }}</h2>
        </div>
        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs chip">
          {{ str_replace('_',' ',$submission->status) }}
        </span>
      </div>

      <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
        <div>
          <div class="muted text-xs">Autor</div>
          <div>{{ $submission->author?->name ?? '—' }}</div>
        </div>
        <div>
          <div class="muted text-xs">Idioma</div>
          <div>{{ $submission->language ?? '—' }}</div>
        </div>
        <div>
          <div class="muted text-xs">Criada</div>
          <div>{{ $submission->created_at?->format('d/m/Y H:i') }}</div>
        </div>
        <div>
          <div class="muted text-xs">Submetida</div>
          <div>{{ $submission->submitted_at?->format('d/m/Y H:i') ?? '—' }}</div>
        </div>
      </div>

      <div class="mt-4">
        <div class="muted text-xs">Resumo</div>
        <p class="mt-1 text-sm">{{ $submission->abstract ?: '—' }}</p>
      </div>

      <div class="mt-4">
        <div class="muted text-xs">Arquivos</div>
        <div class="mt-2 overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-left muted">
              <tr class="border-b" style="border-color:var(--line)">
                <th class="py-2">Tipo</th>
                <th class="py-2">Nome</th>
                <th class="py-2">Tamanho</th>
                <th class="py-2">Ação</th>
              </tr>
            </thead>
            <tbody>
              @forelse($submission->files as $f)
                @php $url = Storage::disk($f->disk ?? 'public')->url($f->path); @endphp
                <tr class="border-b last:border-0" style="border-color:var(--line)">
                  <td class="py-2">{{ $f->role }}</td>
                  <td class="py-2">{{ $f->original_name }}</td>
                  <td class="py-2">{{ $f->size ? number_format($f->size/1024,1,',','.') : '—' }} KB</td>
                  <td class="py-2"><a href="{{ $url }}" target="_blank" class="hover:underline muted">abrir</a></td>
                </tr>
              @empty
                <tr><td class="py-6 muted" colspan="4">Sem arquivos.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Checklist + ações --}}
    <div class="rounded-xl panel border p-4">
      <div class="font-medium">Checklist de Triagem</div>
      <ul class="mt-2 space-y-2 text-sm">
        <li class="flex gap-2 items-start">
          <input type="checkbox" class="mt-1">
          <div>
            <div>Formatação conforme diretrizes</div>
            <div class="muted text-xs">ABNT/APA, estrutura, limites de páginas</div>
          </div>
        </li>
        <li class="flex gap-2 items-start">
          <input type="checkbox" class="mt-1">
          <div>
            <div>Anonimização (duplo-cego)</div>
            <div class="muted text-xs">Sem nomes/afiliações no manuscrito</div>
          </div>
        </li>
        <li class="flex gap-2 items-start">
          <input type="checkbox" class="mt-1">
          <div>
            <div>Escopo e ética</div>
            <div class="muted text-xs">Enquadra-se na revista e sem conflitos éticos aparentes</div>
          </div>
        </li>
        <li class="flex gap-2 items-start">
          <input type="checkbox" class="mt-1">
          <div>
            <div>Originalidade (plágio)</div>
            <div class="muted text-xs">Relatório de similaridade OK</div>
          </div>
        </li>
      </ul>

      <div class="mt-4 font-medium">Ações</div>
      <form method="POST" action="{{ route('admin.submissions.transition',$submission) }}" class="mt-2 space-y-2">
        @csrf
        <textarea name="message" rows="3" placeholder="Mensagem ao autor (opcional)"
                  class="w-full rounded-lg border px-3 py-2 text-sm bg-transparent"
                  style="border-color:var(--line)"></textarea>

        <div class="grid sm:grid-cols-2 gap-2">
          <button name="action" value="desk_reject"   class="rounded-lg px-3 py-2 text-sm border panel">Desk reject</button>
          <button name="action" value="request_fixes" class="rounded-lg px-3 py-2 text-sm border panel">Solicitar correções</button>
          <button name="action" value="send_to_review" class="rounded-lg px-3 py-2 text-sm text-white brand">Enviar à revisão</button>
        </div>
      </form>

      <div class="mt-4 text-xs muted">
        Obs.: estas ações usam o mesmo endpoint de <em>Submissões → transition</em>, mantendo uma única regra de negócio.
      </div>
    </div>
  </div>
@endsection

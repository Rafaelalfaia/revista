@extends('console.layout')
@section('title', 'Submissão #'.$submission->id)
@section('page.title','Submissão')

@section('content')
  <div class="rounded-xl border panel p-4" style="border-color:var(--line)">
    <div class="text-xl font-semibold">{{ $submission->title }}</div>
    <div class="mt-1 text-sm muted">Status: {{ $submission->status }}</div>
  </div>

  <div class="mt-4 grid md:grid-cols-2 gap-3">
    <div class="rounded-xl border panel p-4" style="border-color:var(--line)">
      <div class="font-medium mb-2">Revisores</div>
      @forelse ($submission->reviews as $rv)
        <div class="flex items-center justify-between border-t py-2 first:border-t-0" style="border-color:var(--line)">
          <div>
            <div class="font-medium">{{ $rv->reviewer->name }}</div>
            <div class="text-xs muted">Status do parecer: {{ $rv->status }}</div>
          </div>
          <div class="text-xs text-right">
            @if($rv->requested_corrections_at)
              <div>Correções: {{ $rv->requested_corrections_at->format('d/m/Y H:i') }}</div>
            @endif
            @if($rv->submitted_opinion_at)
              <div>Parecer enviado: {{ $rv->submitted_opinion_at->format('d/m/Y H:i') }}</div>
            @endif
          </div>
        </div>
      @empty
        <div class="muted text-sm">Sem revisores atribuídos.</div>
      @endforelse
    </div>

    <div class="rounded-xl border panel p-4" style="border-color:var(--line)">
      <div class="font-medium mb-2">Ações rápidas</div>
      <div class="flex flex-wrap gap-2">
        @if (Route::has('admin.submissions.read'))
          <a href="{{ route('admin.submissions.read', $submission) }}" class="rounded-lg border panel px-3 h-9 inline-flex items-center text-sm" style="border-color:var(--line)">Ler (modo leitura)</a>
        @endif
        @if (Route::has('admin.submissions.comments.index'))
          <a href="{{ route('admin.submissions.comments.index', $submission) }}" class="rounded-lg border panel px-3 h-9 inline-flex items-center text-sm" style="border-color:var(--line)">Comentários / Correções</a>
        @endif
      </div>
    </div>
  </div>
@endsection

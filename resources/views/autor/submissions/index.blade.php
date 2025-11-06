@extends('console.layout-author')
@section('title','Minhas Submissões — Autor')
@section('page.title','Minhas Submissões')

@section('content')
@php use App\Models\Submission; @endphp

@if (session('ok'))   <div class="mb-4 p-3 rounded bg-green-50 text-green-800">{{ session('ok') }}</div> @endif
@if (session('warn')) <div class="mb-4 p-3 rounded bg-amber-50 text-amber-800">{{ session('warn') }}</div> @endif
@if ($errors->any())
  <div class="mb-4 p-3 rounded bg-red-50 text-red-800">
    <ul class="list-disc ml-5">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
  </div>
@endif

<div class="flex items-center justify-between mb-6">
  <h1 class="text-xl font-bold">Minhas Submissões</h1>
  <a href="{{ route('autor.submissions.create') }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-white brand">Nova submissão</a>
</div>

<div class="space-y-3">
  @forelse ($subs as $s)
    <div class="rounded-2xl panel border p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <div class="flex items-center gap-2">
          <a class="font-semibold hover:text-rose-600" href="{{ route('autor.submissions.wizard',$s) }}">{{ $s->title }}</a>
          <span class="text-xs px-2 py-0.5 rounded-full chip">{{ $s->type_label }}</span>
        </div>
        <div class="text-sm muted">
          Status: <span class="font-medium">{{ $s->status_label }}</span> · Atualizado {{ $s->updated_at->diffForHumans() }}
        </div>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('autor.submissions.wizard',$s) }}" class="px-3 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800">Continuar</a>
        @if ($s->status === Submission::ST_DRAFT)
          <form method="POST" action="{{ route('autor.submissions.destroy',$s) }}" onsubmit="return confirm('Excluir este rascunho?');">
            @csrf @method('DELETE')
            <button class="px-3 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Excluir</button>
          </form>
        @endif
      </div>
    </div>
  @empty
    <div class="rounded-2xl border border-dashed p-8 text-center muted">Você ainda não possui submissões. Crie a primeira!</div>
  @endforelse
</div>

<div class="mt-6">{{ $subs->links() }}</div>
@endsection

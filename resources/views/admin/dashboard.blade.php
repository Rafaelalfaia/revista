@extends('console.layout')
@section('title','Dashboard · Admin')
@section('page.title','Dashboard')
@section('content')
  <div class="mx-auto max-w-5xl">
    <div class="rounded-2xl border border-black/10 dark:border-white/10 bg-white dark:bg-[#0F1412] p-5">
      <p>Você está logado como: <strong>{{ $user->name ?? $user->email }}</strong></p>
      <p class="mt-2">Papéis:
        @foreach(($roles ?? collect()) as $r)
          <span class="inline-block mr-2 rounded-full bg-rose-100 text-rose-700 px-3 py-1 text-sm dark:bg-rose-500/10 dark:text-rose-300">{{ $r }}</span>
        @endforeach
      </p>
    </div>
  </div>
@endsection

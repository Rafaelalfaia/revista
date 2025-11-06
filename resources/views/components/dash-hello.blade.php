@props(['user', 'roles' => collect(), 'title' => 'Dashboard'])

<div class="mx-auto max-w-5xl px-6 py-8">
  <h1 class="text-2xl font-semibold">{{ $title }}</h1>

  <div class="mt-4 rounded-2xl border bg-white p-5">
    <p class="text-slate-700">
      Você está logado como:
      <strong>{{ $user->name ?? $user->email }}</strong>
    </p>

    <p class="mt-2 text-slate-700">
      Papéis (roles):
      @if($roles->isEmpty())
        <span class="inline-block rounded-full bg-slate-200 px-3 py-1 text-sm">sem papel</span>
      @else
        @foreach($roles as $r)
          <span class="mr-2 inline-block rounded-full bg-rose-100 px-3 py-1 text-sm text-rose-700">{{ $r }}</span>
        @endforeach
      @endif
    </p>
  </div>
</div>

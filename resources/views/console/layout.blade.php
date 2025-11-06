<!DOCTYPE html>
<html lang="pt-BR" class="antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title','Console · Trivento')</title>

  <script>
    (() => {
      const KEY = 'trv.theme';
      const saved = localStorage.getItem(KEY) || 'auto';
      const prefersDark = matchMedia('(prefers-color-scheme: dark)').matches;
      const dark = saved === 'dark' || (saved === 'auto' && prefersDark);
      document.documentElement.classList.toggle('dark', dark);
      const meta = document.querySelector('meta[name="theme-color"]') || (() => {
        const m = document.createElement('meta'); m.name = 'theme-color';
        document.head.appendChild(m); return m;
      })();
      meta.setAttribute('content', dark ? '#0B0F0D' : '#FFFFFF');
      window.__TRV_INITIAL_THEME__ = saved;
    })();
  </script>

  <style>
    :root{
      --bg:#FFFFFF; --text:#0F172A; --muted:#475569;
      --panel:#FFFFFF; --panel-2:#F8FAFC; --line:rgba(15,23,42,.10);
      --brand:#E11D48; --brand-700:#BE123C;
      --chip:rgba(225,29,72,.08);
      --glass:rgba(255,255,255,.75); --glass-line:rgba(15,23,42,.10);
      color-scheme: light dark;
    }
    .dark{
      --bg:#0B0F0D; --text:#E5E7EB; --muted:#94A3B8;
      --panel:#0F1412; --panel-2:#111715; --line:rgba(255,255,255,.10);
      --brand:#E11D48; --brand-700:#BE123C;
      --chip:rgba(225,29,72,.10);
      --glass:rgba(8,10,11,.6); --glass-line:rgba(255,255,255,.08);
    }
    @media (prefers-reduced-motion: reduce){
      *{animation:none!important;transition:none!important}
      html:focus-within{scroll-behavior:auto}
    }
    html,body{background:var(--bg);color:var(--text)}
    .panel{background:var(--panel);border-color:var(--line)}
    .panel-2{background:var(--panel-2);border-color:var(--line)}
    .muted{color:var(--muted)}
    .chip{background:var(--chip)}
    .brand{background:var(--brand)} .brand:hover{background:var(--brand-700)}
    .glassbar{
      background:var(--glass);
      -webkit-backdrop-filter:saturate(180%) blur(14px);
      backdrop-filter:saturate(180%) blur(14px);
      border-color:var(--glass-line);
    }
    [x-cloak]{display:none}
  </style>

  <meta name="color-scheme" content="light dark">
  <meta name="theme-color" content="#0B0F0D">
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
</head>
<body
  x-data="shell()"
  x-init="init()"
  @keydown.window.escape="menuOpen ? (menuOpen=false) : null"
  :class="menuOpen ? 'overflow-hidden' : ''"
  class="min-h-[100dvh]"
>
  {{-- Acessibilidade --}}
  <a href="#conteudo"
     class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[60] rounded-md px-3 py-2 text-white"
     style="background:var(--brand)">Ir para o conteúdo</a>

  @php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Route as R;

    $u = auth()->user();

    // Lê primeiro de console.php; se não existir, cai para auth.php
    $cfg = config('console.menu', config('auth.menu', []));

    $roleKey = collect(array_keys($cfg))->first(function($r) use ($u){
        try { return $u && method_exists($u,'hasRole') ? $u->hasRole($r) : false; }
        catch (\Throwable $e) { return false; }
    }) ?? (array_key_exists('Admin',$cfg) ? 'Admin' : (array_key_first($cfg)));

    $menu = $roleKey && isset($cfg[$roleKey]) ? $cfg[$roleKey] : [];
    if (empty($menu) && isset($cfg['Admin'])) $menu = $cfg['Admin'];

    $isAdmin = ($roleKey === 'Admin') || ($u && method_exists($u,'hasRole') && $u->hasRole('Admin'));

    // Ações (mesma lógica de fallback)
    $actionsCfg = config('console.actions', config('auth.actions', []));
    $act = collect($actionsCfg)->first(fn($v,$pattern) => request()->routeIs($pattern));
    $actRouteExists = $act && isset($act['route']) && R::has($act['route']);
    $canAct = $act && ($isAdmin || empty($act['can']) || ($u && $u->can($act['can'])));

    $isRouteActive = function(string $routeName) {
        $base = Str::of($routeName)->beforeLast('.');
        return request()->routeIs($routeName) || request()->routeIs($base.'.*');
    };
    @endphp


  <div class="grid min-h-[100dvh] lg:grid-cols-[260px_1fr]">

    {{-- SIDEBAR --}}
    <aside id="console-sidebar"
      class="fixed inset-y-0 left-0 z-50 w-[86%] max-w-[320px] -translate-x-full panel border px-4 py-4 transition-transform duration-200 ease-out
             lg:static lg:translate-x-0 lg:w-auto lg:h-[100dvh] lg:sticky lg:top-0 lg:overflow-y-auto"
      :class="menuOpen ? 'translate-x-0' : '-translate-x-full'"
      role="navigation" aria-label="Menu lateral"
      x-trap.noscroll.inert="menuOpen">

      <div class="flex items-center justify-between gap-2 px-2">
        <div class="flex items-center gap-3">
          <div class="h-2.5 w-2.5 rounded-full" style="background:var(--brand)"></div>
          <span class="font-semibold">Trivento · Console</span>
        </div>
        <button class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg chip"
                @click="menuOpen=false" :aria-expanded="menuOpen.toString()" aria-label="Fechar menu">
          <svg width="20" height="20" fill="none" class="muted"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5"/></svg>
        </button>
      </div>

      <nav class="mt-6 space-y-1 text-sm">
        @foreach ($menu as $it)
          @php
            $rt    = $it['route'] ?? '#';
            $can   = $it['can']   ?? null;

            $show  = !$can || ($u && $u->can($can)) || $isAdmin;

            $routeExists = $rt !== '#' && R::has($rt);
            $href   = $routeExists ? route($rt) : '#';
            $active = $routeExists && $isRouteActive($rt);
          @endphp
          @if ($show)
            <x-console.navlink href="{{ $href }}" :active="$active">
              {{ $it['label'] }}
            </x-console.navlink>
          @endif
        @endforeach
      </nav>

      <div class="mt-8 border-t pt-4" style="border-color:var(--line);">
        <div class="text-xs muted">Sessão</div>
        @php
          $avatar = ($u && !empty($u->avatar_url)) ? $u->avatar_url : asset('images/avatar.png');
        @endphp
        <div class="mt-2 flex items-center gap-3">
          <img src="{{ $avatar }}" class="h-9 w-9 rounded-full object-cover" alt="Foto de {{ $u?->name ?? 'usuário' }}">
          <div class="leading-tight">
            <div class="text-sm font-medium line-clamp-1">{{ $u?->name }}</div>
            <div class="text-xs muted line-clamp-1">{{ $u?->email }}</div>
          </div>
        </div>
        @if (Route::has('logout'))
          <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf
            <button type="submit" class="text-sm muted hover:text-rose-400">Sair</button>
          </form>
        @endif
      </div>
    </aside>

    <div x-show="menuOpen" x-transition.opacity
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         @click="menuOpen=false" aria-hidden="true"></div>

    <main class="relative overflow-x-hidden">

      <div class="sticky top-0 z-40 border-b glassbar">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
          <div class="h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <button class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg chip"
                      @click="openMenu($el)" :aria-expanded="menuOpen.toString()" aria-controls="console-sidebar" aria-label="Abrir menu">
                <svg width="20" height="20" fill="none" class="muted">
                  <path d="M3 6h14M3 12h14M3 18h10" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <div class="font-semibold">@yield('page.title','Dashboard')</div>
            </div>

            <div class="flex items-center gap-2">
              @if ($canAct && $actRouteExists)
                <a href="{{ route($act['route']) }}"
                   class="hidden sm:inline-flex items-center rounded-lg px-3 h-9 text-sm text-white brand">
                   {{ $act['label'] }}
                </a>
              @endif

              <div x-data="{open:false}" class="relative" x-cloak>
                <button id="theme-btn" @click="open=!open"
                        class="inline-flex items-center gap-2 rounded-lg chip px-3 h-9 text-sm"
                        :aria-expanded="open.toString()" aria-haspopup="menu" aria-controls="theme-menu">
                  <template x-if="theme==='dark'">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 1 0 9.79 9.79Z"/></svg>
                  </template>
                  <template x-if="theme==='light'">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"/></svg>
                  </template>
                  <template x-if="theme==='auto'">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M6 12a6 6 0 0 0 12 0 6 6 0 1 0-12 0z"/></svg>
                  </template>
                  <span class="text-sm" x-text="labelTheme()"></span>
                </button>

                <div x-show="open" @click.outside="open=false" @keydown.escape.stop="open=false"
                     id="theme-menu" role="menu" aria-labelledby="theme-btn"
                     class="absolute right-0 mt-2 w-44 rounded-xl panel border p-2 z-50">
                  <button role="menuitemradio" :aria-checked="theme==='auto'" @click="setTheme('auto'); open=false"
                          class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5"
                          :class="theme==='auto' ? 'text-rose-500 dark:text-rose-300 font-medium' : ''">
                    Automático
                  </button>
                  <button role="menuitemradio" :aria-checked="theme==='light'" @click="setTheme('light'); open=false"
                          class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5"
                          :class="theme==='light' ? 'text-rose-500 dark:text-rose-300 font-medium' : ''">
                    Modo claro
                  </button>
                  <button role="menuitemradio" :aria-checked="theme==='dark'" @click="setTheme('dark'); open=false"
                          class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5"
                          :class="theme==='dark' ? 'text-rose-500 dark:text-rose-300 font-medium' : ''">
                    Modo escuro
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="border-b panel-2">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-2">
          @stack('context')
        </div>
      </div>

      <div id="conteudo" class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-4 pb-[calc(env(safe-area-inset-bottom)+88px)]">
        <div class="rounded-2xl panel-2 border p-4 sm:p-6">
          @yield('content')
        </div>

        @if (request()->routeIs('*dashboard*') || request()->routeIs('dashboard'))
          <div class="mt-4">@includeIf('console.partials._banner_principal')</div>
        @endif
      </div>
    </main>
  </div>

  @php
    $visible = collect($menu)->filter(function($it) use ($u,$isAdmin){
      $can = $it['can'] ?? null;
      return !$can || ($u && $u->can($can)) || $isAdmin;
    })->filter(function($it){ // exige rota existente
      return isset($it['route']) && \Illuminate\Support\Facades\Route::has($it['route']);
    })->values();

    if ($visible->isEmpty() && isset($cfg['Admin'])) {
      $visible = collect($cfg['Admin'])->filter(function($it){
        return isset($it['route']) && \Illuminate\Support\Facades\Route::has($it['route']);
      })->values();
    }

    $tabs = $visible->take(2)->all();
  @endphp

  <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 border-t glassbar">
    <div class="grid grid-cols-3 text-xs">
      @for ($i=0; $i < 2; $i++)
        @php $it = $tabs[$i] ?? null; @endphp
        @if ($it)
          @php $active = $isRouteActive($it['route']); @endphp
          <a href="{{ route($it['route']) }}"
             class="flex flex-col items-center py-2 {{ $active ? 'text-rose-500' : '' }}"
             aria-current="{{ $active ? 'page' : 'false' }}">
            <svg width="20" height="20" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.5"/></svg>
            <span>{{ $it['label'] }}</span>
          </a>
        @else
          <span></span>
        @endif
      @endfor

      <button type="button" @click="cycleTheme()" class="flex flex-col items-center py-2">
        <svg width="20" height="20" fill="none"><path d="M12 3a9 9 0 1 0 9 9c0-3.78-2.1-7.06-5.19-8.66A6 6 0 0 1 12 3Z" stroke="currentColor" stroke-width="1.5"/></svg>
        <span>Tema</span>
      </button>
    </div>
  </nav>

  <script>
    function shell(){
      return {
        menuOpen: JSON.parse(localStorage.getItem('trv.menu') || 'false'),
        theme: window.__TRV_INITIAL_THEME__ || 'auto',
        mql: null,
        lastBtn: null,
        init(){
          this.mql = matchMedia('(prefers-color-scheme: dark)');
          this.mql.addEventListener?.('change', () => { if (this.theme==='auto') this.apply(this.theme); });
          this.apply(this.theme);
        },
        openMenu(btn){ this.lastBtn = btn; this.menuOpen = true; localStorage.setItem('trv.menu','true'); },
        closeMenu(){ this.menuOpen = false; localStorage.setItem('trv.menu','false'); this.lastBtn?.focus?.(); },
        labelTheme(){ return this.theme==='auto' ? 'Automático' : (this.theme==='dark' ? 'Escuro' : 'Claro'); },
        cycleTheme(){ this.setTheme(this.theme==='auto' ? 'light' : this.theme==='light' ? 'dark' : 'auto'); },
        setTheme(t){ this.theme=t; localStorage.setItem('trv.theme', t); this.apply(t); },
        apply(t){
          const prefersDark = this.mql?.matches ?? false;
          const dark = t==='dark' || (t==='auto' && prefersDark);
          document.documentElement.classList.toggle('dark', dark);
          const meta = document.querySelector('meta[name="theme-color"]');
          if (meta) meta.setAttribute('content', dark ? '#0B0F0D' : '#FFFFFF');
        }
      }
    }
  </script>

  @stack('scripts')
</body>
</html>

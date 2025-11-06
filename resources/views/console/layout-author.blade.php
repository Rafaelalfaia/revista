{{-- resources/views/console/layout-author.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR" class="antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title','Autor · Trivento')</title>

  {{-- ⚡ Aplica tema antes do CSS (auto/claro/escuro) --}}
  <script>
    (() => {
      const KEY = 'trv.theme';
      const saved = localStorage.getItem(KEY) || 'auto';
      const prefersDark = matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
      const dark = saved === 'dark' || (saved === 'auto' && prefersDark);
      document.documentElement.classList.toggle('dark', dark);
      // theme-color inicial (Safari/Android)
      const meta = document.querySelector('meta[name="theme-color"]') || (() => {
        const m = document.createElement('meta'); m.name = 'theme-color';
        document.head.appendChild(m); return m;
      })();
      meta.setAttribute('content', dark ? '#0B0F0D' : '#FFFFFF');
      window.__TRV_INITIAL_THEME__ = saved;
    })();
  </script>

  {{-- 🎨 Tokens (rosa + claro/escuro) --}}
  <style>
    :root{
      --bg:#FFFFFF; --text:#0F172A; --muted:#475569;
      --panel:#FFFFFF; --panel-2:#F8FAFC; --line:rgba(15,23,42,.10);
      --brand:#E11D48; --brand-700:#BE123C; --chip:rgba(225,29,72,.08);
      --glass:rgba(255,255,255,.75); --glass-line:rgba(15,23,42,.10);
    }
    .dark{
      --bg:#0B0F0D; --text:#E5E7EB; --muted:#94A3B8;
      --panel:#0F1412; --panel-2:#111715; --line:rgba(255,255,255,.10);
      --brand:#E11D48; --brand-700:#BE123C; --chip:rgba(225,29,72,.10);
      --glass:rgba(8,10,11,.6); --glass-line:rgba(255,255,255,.08);
    }
    html,body{background:var(--bg); color:var(--text);}
    .panel{background:var(--panel); border-color:var(--line);}
    .panel-2{background:var(--panel-2); border-color:var(--line);}
    .muted{color:var(--muted);}
    .chip{background:var(--chip);}
    .brand{background:var(--brand);}
    .brand:hover{background:var(--brand-700);}
    .glassbar{
      background:var(--glass);
      -webkit-backdrop-filter:saturate(180%) blur(14px);
      backdrop-filter:saturate(180%) blur(14px);
      border-color:var(--glass-line);
    }
  </style>

  <meta name="theme-color" content="#0B0F0D">
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
  <style>[x-cloak]{display:none}</style>
</head>
<body x-data="shell()" x-init="init()" :class="menuOpen ? 'overflow-hidden':''" class="min-h-[100dvh]">

  <a href="#conteudo"
     class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-50 rounded-md px-3 py-2 text-white"
     style="background:var(--brand)">Ir para o conteúdo</a>

  <div class="grid min-h-[100dvh] lg:grid-cols-[260px_1fr]">

    {{-- SIDEBAR (Autor) --}}
    <aside id="console-sidebar"
      class="fixed inset-y-0 left-0 z-50 w-[86%] max-w-[320px] -translate-x-full panel border px-4 py-4 transition-transform duration-200 ease-out
             lg:static lg:translate-x-0 lg:w-auto lg:h-[100dvh] lg:sticky lg:top-0 lg:overflow-y-auto"
      :class="menuOpen ? 'translate-x-0' : '-translate-x-full'"
      role="navigation" aria-label="Menu lateral">

      <div class="flex items-center justify-between gap-2 px-2">
        <div class="flex items-center gap-3">
          <div class="h-2.5 w-2.5 rounded-full" style="background:var(--brand)"></div>
          <span class="font-semibold">Trivento · Autor</span>
        </div>
        <button class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg chip"
                @click="closeMenu()" aria-label="Fechar menu" aria-controls="console-sidebar">
          <svg width="20" height="20" fill="none" class="muted"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5"/></svg>
        </button>
      </div>

      @php
        $isAutorDash = request()->routeIs('autor.dashboard');
        $isAutorSubs = request()->routeIs('autor.submissions.*');
      @endphp

      <nav class="mt-6 space-y-1 text-sm">
        <x-console.navlink href="{{ Route::has('autor.dashboard') ? route('autor.dashboard') : '#' }}" :active="$isAutorDash">Dashboard</x-console.navlink>

        <div class="mt-4 mb-1 px-2 text-[10px] uppercase tracking-wide muted">Fluxo do Autor</div>
        @if (Route::has('autor.submissions.index'))
          <x-console.navlink href="{{ route('autor.submissions.index') }}" :active="$isAutorSubs">Minhas Submissões</x-console.navlink>
        @else
          <x-console.navlink href="#">Minhas Submissões</x-console.navlink>
        @endif

        @if (Route::has('autor.submissions.create'))
          <x-console.navlink href="{{ route('autor.submissions.create') }}">Nova Submissão</x-console.navlink>
        @else
          <x-console.navlink href="#">Nova Submissão</x-console.navlink>
        @endif

        <div class="mt-4 mb-1 px-2 text-[10px] uppercase tracking-wide muted">Conta</div>
        <x-console.navlink href="#">Notificações</x-console.navlink>
        <x-console.navlink href="#">Meu Perfil</x-console.navlink>
      </nav>

      {{-- Sessão do usuário --}}
      <div class="mt-8 border-t pt-4" style="border-color:var(--line);">
        <div class="text-xs muted">Sessão</div>
        @php
          $user = auth()->user();
          $avatar = ($user && !empty($user->avatar_url)) ? $user->avatar_url : asset('images/avatar.png');
        @endphp
        <div class="mt-2 flex items-center gap-3">
          <img src="{{ $avatar }}" class="h-9 w-9 rounded-full object-cover" alt="Foto do usuário">
          <div class="leading-tight">
            <div class="text-sm font-medium line-clamp-1">{{ $user?->name }}</div>
            <div class="text-xs muted line-clamp-1">{{ $user?->email }}</div>
          </div>
        </div>
        @if (Route::has('logout'))
          <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf
            <button type="submit" class="text-sm muted hover:text-rose-400">Sair</button>
          </form>
        @endif
      </div>
    </aside>

    {{-- BACKDROP mobile --}}
    <div x-show="menuOpen" x-transition.opacity
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         @click="closeMenu()" aria-hidden="true"></div>

    {{-- MAIN --}}
    <main class="relative overflow-x-hidden">

      {{-- App Bar translúcida --}}
      <div class="sticky top-0 z-40 border-b glassbar">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
          <div class="h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <button class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg chip"
                      @click="openMenu($el)" aria-controls="console-sidebar" aria-label="Abrir menu">
                <svg width="20" height="20" fill="none" class="muted">
                  <path d="M3 6h14M3 12h14M3 18h10" stroke="currentColor" stroke-width="1.5"/>
                </svg>
              </button>
              <div class="font-semibold">@yield('page.title','Dashboard do Autor')</div>
              @hasSection('page.breadcrumbs')
                <div class="hidden sm:flex items-center gap-2 text-xs muted">
                  @yield('page.breadcrumbs')
                </div>
              @endif
            </div>

            <div class="flex items-center gap-2">
              {{-- Slot para ações da página (opcional) --}}
              @yield('page.actions')

              {{-- Ação rápida: Nova submissão --}}
              @if (Route::has('autor.submissions.create'))
                <a href="{{ route('autor.submissions.create') }}"
                   class="hidden sm:inline-flex items-center rounded-lg px-3 h-9 text-sm text-white brand">
                  + Nova
                </a>
              @endif

              {{-- Troca de tema --}}
              <div x-data="{open:false}" class="relative" x-cloak>
                <button @click="open=!open" class="inline-flex items-center gap-2 rounded-lg chip px-3 h-9 text-sm" aria-haspopup="menu" :aria-expanded="open">
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
                <div x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 w-44 rounded-xl panel border p-2 z-50" role="menu">
                  <button @click="setTheme('auto');  open=false" class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5" :class="theme==='auto'  ? 'text-rose-500 dark:text-rose-300 font-medium':''">Automático</button>
                  <button @click="setTheme('light'); open=false" class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5" :class="theme==='light' ? 'text-rose-500 dark:text-rose-300 font-medium':''">Modo claro</button>
                  <button @click="setTheme('dark');  open=false" class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/40 dark:hover:bg-white/5" :class="theme==='dark'  ? 'text-rose-500 dark:text-rose-300 font-medium':''">Modo escuro</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Conteúdo principal (cartão app) --}}
      <div id="conteudo" class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-4 pb-[calc(env(safe-area-inset-bottom)+88px)]">
        <div class="rounded-2xl panel-2 border p-4 sm:p-6">
          @yield('content')
        </div>
      </div>
    </main>
  </div>

  {{-- Tab Bar (mobile) --}}
  @php
    $tabHomeActive = request()->routeIs('autor.dashboard');
    $tabSubsActive = request()->routeIs('autor.submissions.*');
  @endphp
  <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 border-t glassbar" role="navigation" aria-label="Abas inferior">
    <div class="grid grid-cols-4 text-xs">
      <a href="{{ Route::has('autor.dashboard') ? route('autor.dashboard') : '#' }}"
         class="flex flex-col items-center py-2 {{ $tabHomeActive ? 'text-rose-600' : '' }}" aria-current="{{ $tabHomeActive ? 'page' : 'false' }}">
        <svg width="20" height="20" fill="none"><path d="M3 11l9-7 9 7v8a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8Z" stroke="currentColor" stroke-width="1.5"/></svg>
        <span>Home</span>
      </a>
      <a href="{{ Route::has('autor.submissions.index') ? route('autor.submissions.index') : '#' }}"
         class="flex flex-col items-center py-2 {{ $tabSubsActive ? 'text-rose-600' : '' }}" aria-current="{{ $tabSubsActive ? 'page' : 'false' }}">
        <svg width="20" height="20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/></svg>
        <span>Submissões</span>
      </a>
      <a href="#" class="flex flex-col items-center py-2">
        <svg width="20" height="20" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.5"/></svg>
        <span>Relatórios</span>
      </a>
      <button type="button" @click="cycleTheme()" class="flex flex-col items-center py-2" aria-label="Alternar tema">
        <svg width="20" height="20" fill="none"><path d="M12 3a9 9 0 1 0 9 9c0-3.78-2.1-7.06-5.19-8.66A6 6 0 0 1 12 3Z" stroke="currentColor" stroke-width="1.5"/></svg>
        <span>Tema</span>
      </button>
    </div>
  </nav>

  {{-- FAB para nova submissão (mobile) --}}
  @if (Route::has('autor.submissions.create'))
    <a href="{{ route('autor.submissions.create') }}"
       class="lg:hidden fixed bottom-[88px] right-4 z-40 inline-flex items-center justify-center h-12 w-12 rounded-full text-white shadow-xl"
       style="background:var(--brand)" aria-label="Nova submissão">+</a>
  @endif

  <script>
    function shell(){
      return {
        menuOpen: JSON.parse(localStorage.getItem('trv.menu') || 'false'),
        theme: window.__TRV_INITIAL_THEME__ || 'auto',
        mql: null, lastMenuBtn:null,
        init(){
          this.mql = matchMedia('(prefers-color-scheme: dark)');
          this.mql.addEventListener?.('change', () => { if (this.theme==='auto') this.apply(this.theme); });
          this.apply(this.theme);

          // Fechar com ESC no mobile/desktop
          window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.menuOpen) this.closeMenu();
          });
        },
        openMenu(btn){ this.lastMenuBtn=btn; this.menuOpen=true; localStorage.setItem('trv.menu','true'); },
        closeMenu(){ this.menuOpen=false; localStorage.setItem('trv.menu','false'); this.lastMenuBtn?.focus?.(); },
        labelTheme(){ return this.theme==='auto' ? 'Automático' : (this.theme==='dark' ? 'Escuro' : 'Claro'); },
        cycleTheme(){ this.setTheme(this.theme==='auto' ? 'light' : this.theme==='light' ? 'dark' : 'auto'); },
        setTheme(t){ this.theme=t; localStorage.setItem('trv.theme',t); this.apply(t); },
        apply(t){
          const prefersDark = this.mql?.matches ?? false;
          const dark = t==='dark' || (t==='auto' && prefersDark);
          document.documentElement.classList.toggle('dark', dark);
          const meta = document.querySelector('meta[name="theme-color"]');
          if(meta) meta.setAttribute('content', dark ? '#0B0F0D' : '#FFFFFF');
        }
      }
    }
  </script>

  @stack('scripts')
</body>
</html>

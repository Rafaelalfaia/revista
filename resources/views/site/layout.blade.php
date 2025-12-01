<!DOCTYPE html>
<html lang="pt-BR" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title','Revista Trivento')</title>
    <meta name="theme-color" content="#FFFFFF">

    <script>
        (() => {
            const KEY = 'trv.site.theme';
            const saved = localStorage.getItem(KEY) || 'auto';
            const prefersDark = typeof matchMedia === 'function' ? matchMedia('(prefers-color-scheme: dark)').matches : false;
            const dark = saved === 'dark' || (saved === 'auto' && prefersDark);
            document.documentElement.classList.toggle('dark', dark);
            window.__TRV_SITE_THEME__ = saved;
            const existingMeta = document.querySelector('meta[name="theme-color"]');
            const meta = existingMeta || (() => {
                const m = document.createElement('meta');
                m.name = 'theme-color';
                document.head.appendChild(m);
                return m;
            })();
            meta.setAttribute('content', dark ? '#050608' : '#FFFFFF');
        })();
    </script>

    <style>
        :root{
            --bg:#FFFFFF;
            --text:#0F172A;
            --muted:#64748B;
            --panel:#FFFFFF;
            --panel-soft:#F8FAFC;
            --line:rgba(148,163,184,.3);
            --brand:#E11D48;
            --brand-700:#BE123C;
            --chip:rgba(225,29,72,.06);
            --chip-line:rgba(225,29,72,.2);
            --glass:rgba(255,255,255,.82);
            --glass-line:rgba(148,163,184,.25);
        }
        .dark{
            --bg:#020617;
            --text:#E5E7EB;
            --muted:#94A3B8;
            --panel:#020617;
            --panel-soft:#020617;
            --line:rgba(15,23,42,.7);
            --brand:#FB7185;
            --brand-700:#F43F5E;
            --chip:rgba(248,113,133,.15);
            --chip-line:rgba(248,113,133,.5);
            --glass:rgba(15,23,42,.92);
            --glass-line:rgba(30,64,175,.4);
        }

        html,body{
            background:
                radial-gradient(circle at top,var(--panel-soft) 0,transparent 55%),
                var(--bg);
            color:var(--text);
            min-height:100%;
        }
        body{
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
        }

        .site-root{
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        .site-header{
            background:var(--glass);
            -webkit-backdrop-filter:saturate(170%) blur(16px);
            backdrop-filter:saturate(170%) blur(16px);
            border-bottom:1px solid var(--glass-line);
        }
        .site-header-inner{
            max-width:80rem;
            margin:0 auto;
            padding:.6rem 1rem;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.75rem;
        }

        .site-logo-wrap{
            display:flex;
            align-items:center;
            gap:.6rem;
            min-width:0;
            text-decoration:none;
            color:inherit;
        }
        .site-logo-img{
            height:40px;
            width:auto;
            border-radius:1rem;
            padding:.25rem .5rem;
            background:linear-gradient(135deg,rgba(255,255,255,.98),rgba(248,250,252,.9));
            box-shadow:0 10px 30px rgba(15,23,42,.26);
        }
        .dark .site-logo-img{
            background:linear-gradient(135deg,#020617,#020617);
            box-shadow:0 20px 40px rgba(0,0,0,.8);
        }
        .logo-dark{display:none;}
        .dark .logo-light{display:none;}
        .dark .logo-dark{display:block;}

        .site-title-wrap{
            display:flex;
            flex-direction:column;
            min-width:0;
        }
        .site-title{
            font-size:.9rem;
            font-weight:600;
            line-height:1.2;
            white-space:normal;
        }
        .site-subtitle{
            font-size:.7rem;
            color:var(--muted);
            line-height:1.2;
            white-space:normal;
        }
        @media(min-width:640px){
            .site-title,
            .site-subtitle{
                white-space:nowrap;
            }
        }

        .site-header-right{
            display:flex;
            align-items:center;
            gap:.5rem;
        }

        .site-nav-desktop{
            display:none;
            align-items:center;
            gap:.35rem;
            font-size:.82rem;
        }
        @media(min-width:768px){
            .site-nav-desktop{
                display:flex;
            }
        }

        .site-nav-link{
            padding:.45rem .9rem;
            border-radius:999px;
            color:var(--muted);
            border:1px solid transparent;
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            transition:
                background .15s,
                color .15s,
                border-color .15s,
                box-shadow .15s,
                transform .1s;
        }
        .site-nav-link:hover{
            background:var(--panel-soft);
            color:var(--text);
            border-color:var(--line);
        }
        .site-nav-link-active{
            color:#ffffff;
            background:linear-gradient(135deg,var(--brand),var(--brand-700));
            border-color:transparent;
            box-shadow:0 12px 24px rgba(225,29,72,.35);
            transform:translateY(-1px);
        }

        .site-theme-toggle{
            border-radius:999px;
            padding:.35rem .7rem;
            font-size:.75rem;
            border:1px solid var(--line);
            background:var(--chip);
            color:var(--muted);
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            white-space:nowrap;
        }
        .site-theme-toggle svg{
            width:18px;
            height:18px;
        }

        .site-main{
            flex:1 1 auto;
            max-width:80rem;
            width:100%;
            margin:0 auto;
            padding:1rem 1rem 5rem;
        }
        @media(min-width:768px){
            .site-main{
                padding:1.25rem 1.5rem 2.5rem;
            }
        }

        .bottom-nav{
            background:var(--glass);
            -webkit-backdrop-filter:saturate(160%) blur(18px);
            backdrop-filter:saturate(160%) blur(18px);
            border-color:var(--glass-line);
            border-top-left-radius:1.25rem;
            border-top-right-radius:1.25rem;
        }
        .bottom-grid{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            max-width:28rem;
            margin:0 auto;
            font-size:.7rem;
        }
        .bottom-tab{
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            gap:.15rem;
            padding:.45rem 0 .35rem;
            color:var(--muted);
        }
        .bottom-tab-icon-wrap{
            display:flex;
            align-items:center;
            justify-content:center;
            width:2.25rem;
            height:2.25rem;
            border-radius:999px;
            transition:
                background .15s,
                box-shadow .15s,
                transform .12s,
                color .15s;
        }
        .bottom-tab svg{
            width:20px;
            height:20px;
        }
        .bottom-tab span{
            font-size:.7rem;
        }
        .bottom-tab-active{
            color:var(--brand);
            font-weight:500;
        }
        .bottom-tab-active .bottom-tab-icon-wrap{
            background:radial-gradient(circle at top,var(--brand) 0,transparent 70%);
            box-shadow:
                0 0 0 1px rgba(248,250,252,.08),
                0 10px 20px rgba(225,29,72,.45);
            transform:translateY(-1px);
        }
        .bottom-tab-active::after{
            content:'';
            margin-top:.1rem;
            width:22px;
            height:2px;
            border-radius:999px;
            background:var(--brand);
        }

        @media(min-width:1024px){
            .bottom-nav{
                display:none;
            }
        }

        [x-cloak]{
            display:none;
        }
    </style>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('head')
</head>
<body x-data="siteShell()" x-init="init()" class="site-root">

<a href="#conteudo"
   class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-50 rounded-md px-3 py-2 text-white"
   style="background:var(--brand)">
    Ir para o conteúdo
</a>

@php
    $logoLight = asset('images/logo.png');
    $logoDark  = asset('images/logo1.png');

    $homeUrl       = \Illuminate\Support\Facades\Route::has('site.home') ? route('site.home') : url('/');
    $pubUrl        = \Illuminate\Support\Facades\Route::has('site.submissions.index')
                        ? route('site.submissions.index')
                        : (\Illuminate\Support\Facades\Route::has('site.articles.index')
                            ? route('site.articles.index')
                            : $homeUrl);
    $editionsUrl   = \Illuminate\Support\Facades\Route::has('site.editions.index')
                        ? route('site.editions.index')
                        : (\Illuminate\Support\Facades\Route::has('admin.editions.index')
                            ? route('admin.editions.index')
                            : '#');
    $categoriesUrl = \Illuminate\Support\Facades\Route::has('site.categories.index')
                        ? route('site.categories.index')
                        : '#';
    $loginUrl      = \Illuminate\Support\Facades\Route::has('login')
                        ? route('login')
                        : '#';

    $isHome       = request()->routeIs('site.home');
    $isPub        = request()->routeIs('site.submissions.*') || request()->routeIs('site.articles.*');
    $isEditions   = request()->routeIs('site.editions.*') || request()->routeIs('admin.editions.*');
    $isCategories = request()->routeIs('site.categories.*');
    $isLogin      = request()->routeIs('login') || request()->routeIs('register');

    $tabPubActive        = $isHome || $isPub;
    $tabEditionsActive   = $isEditions;
    $tabCategoriesActive = $isCategories;
    $tabLoginActive      = $isLogin;
@endphp

<header class="site-header sticky top-0 z-40">
    <div class="site-header-inner">
        <a href="{{ $homeUrl }}" class="site-logo-wrap">
            <img src="{{ $logoLight }}" alt="Revista Trivento" class="site-logo-img logo-light">
            <img src="{{ $logoDark }}" alt="Revista Trivento" class="site-logo-img logo-dark">
            <div class="site-title-wrap">
                <div class="site-title">Revista Trivento Educação</div>
                <div class="site-subtitle">Publicações científicas em acesso aberto</div>
            </div>
        </a>

        <div class="site-header-right">
            <nav class="site-nav-desktop">
                <a href="{{ $pubUrl }}" class="site-nav-link {{ $tabPubActive ? 'site-nav-link-active' : '' }}">
                    <span>Página Principal</span>
                </a>
                <a href="{{ $editionsUrl }}" class="site-nav-link {{ $tabEditionsActive ? 'site-nav-link-active' : '' }}">
                    <span>Edições</span>
                </a>
                <a href="{{ $categoriesUrl }}" class="site-nav-link {{ $tabCategoriesActive ? 'site-nav-link-active' : '' }}">
                    <span>Categorias</span>
                </a>
                <a href="{{ $loginUrl }}" class="site-nav-link {{ $tabLoginActive ? 'site-nav-link-active' : '' }}">
                    <span>Entrar</span>
                </a>
            </nav>

            <div x-data="{open:false}" class="relative" x-cloak>
                <button type="button"
                        @click="open=!open"
                        class="site-theme-toggle"
                        aria-haspopup="menu"
                        :aria-expanded="open">
                    <template x-if="theme==='dark'">
                        <svg viewBox="0 0 24 24">
                            <path fill="currentColor" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/>
                        </svg>
                    </template>
                    <template x-if="theme==='light'">
                        <svg viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 4a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V5a1 1 0 0 1 1-1Zm0 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7-3a1 1 0 0 1 1 1 8 8 0 0 1-.5 2.77 1 1 0 1 1-1.88-.68A6 6 0 0 0 18 13a1 1 0 0 1 1-1ZM5 12a1 1 0 0 1 1-1 6 6 0 0 0 1.38-.17 1 1 0 1 1 .49 1.94A8 8 0 0 1 5 13a1 1 0 0 1-1-1Zm2.64 5.36a1 1 0 0 1 1.4 0A6 6 0 0 0 12 18a6 6 0 0 0 2.95-.79 1 1 0 0 1 1 1.74A8 8 0 0 1 4 13a1 1 0 0 1 2 0 6 6 0 0 0 1.64 4.36Z"/>
                        </svg>
                    </template>
                    <template x-if="theme==='auto'">
                        <svg viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 3a1 1 0 0 1 1 1v1.06A7 7 0 0 1 19.94 11H21a1 1 0 0 1 0 2h-1.06A7 7 0 0 1 13 19.94V21a1 1 0 0 1-2 0v-1.06A7 7 0 0 1 4.06 13H3a1 1 0 1 1 0-2h1.06A7 7 0 0 1 11 5.06V4a1 1 0 0 1 1-1Z"/>
                        </svg>
                    </template>
                    <span x-text="labelTheme()"></span>
                </button>

                <div x-show="open"
                     @click.outside="open=false"
                     class="absolute right-0 mt-2 w-40 rounded-xl border bg-[var(--panel)] text-[var(--text)] text-sm shadow-xl overflow-hidden">
                    <button type="button"
                            @click="setTheme('auto');open=false"
                            class="w-full px-3 py-2 text-left hover:bg-[var(--panel-soft)]"
                            :class="theme==='auto' ? 'text-rose-500 font-medium' : ''">
                        Automático
                    </button>
                    <button type="button"
                            @click="setTheme('light');open=false"
                            class="w-full px-3 py-2 text-left hover:bg-[var(--panel-soft)]"
                            :class="theme==='light' ? 'text-rose-500 font-medium' : ''">
                        Modo claro
                    </button>
                    <button type="button"
                            @click="setTheme('dark');open=false"
                            class="w-full px-3 py-2 text-left hover:bg-[var(--panel-soft)]"
                            :class="theme==='dark' ? 'text-rose-500 font-medium' : ''">
                        Modo escuro
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<main id="conteudo" class="site-main">
    @yield('content')
</main>

<nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bottom-nav" aria-label="Navegação principal">
    <div class="bottom-grid">
        <a href="{{ $pubUrl }}"
           class="bottom-tab {{ $tabPubActive ? 'bottom-tab-active' : '' }}"
           aria-current="{{ $tabPubActive ? 'page' : 'false' }}">
            <div class="bottom-tab-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M8 4.75h5.5L18 9.25V19a1.25 1.25 0 0 1-1.25 1.25H8A1.25 1.25 0 0 1 6.75 19V6A1.25 1.25 0 0 1 8 4.75Z"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.5 4.75V9.5H18"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span>Principal</span>
        </a>

        <a href="{{ $editionsUrl }}"
           class="bottom-tab {{ $tabEditionsActive ? 'bottom-tab-active' : '' }}"
           aria-current="{{ $tabEditionsActive ? 'page' : 'false' }}">
            <div class="bottom-tab-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M7 4.75h10a1.25 1.25 0 0 1 1.25 1.25V18A1.25 1.25 0 0 1 17 19.25H7A1.25 1.25 0 0 1 5.75 18V6A1.25 1.25 0 0 1 7 4.75Z"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9.5 8H15M9.5 12H15M9.5 16H13"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span>Edições</span>
        </a>

        <a href="{{ $categoriesUrl }}"
           class="bottom-tab {{ $tabCategoriesActive ? 'bottom-tab-active' : '' }}"
           aria-current="{{ $tabCategoriesActive ? 'page' : 'false' }}">
            <div class="bottom-tab-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M5 5.75h5.5V11.5H5Z"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.5 5.75H19V11.5h-5.5Z"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 13.25h5.5V19H5Z"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.5 13.25H19V19h-5.5Z"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span>Categorias</span>
        </a>

        <a href="{{ $loginUrl }}"
           class="bottom-tab {{ $tabLoginActive ? 'bottom-tab-active' : '' }}"
           aria-current="{{ $tabLoginActive ? 'page' : 'false' }}">
            <div class="bottom-tab-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M13.25 5H18a1.25 1.25 0 0 1 1.25 1.25v11.5A1.25 1.25 0 0 1 18 19H13.25"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11 16.25 7.25 12.5 11 8.75"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.25 12.5H15.5"
                          stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span>Entrar</span>
        </a>
    </div>
</nav>

<script>
    function siteShell(){
        return {
            theme: window.__TRV_SITE_THEME__ || 'auto',
            mql: null,
            init(){
                if (typeof window.matchMedia === 'function') {
                    this.mql = window.matchMedia('(prefers-color-scheme: dark)');
                    if (this.mql && this.mql.addEventListener) {
                        this.mql.addEventListener('change', () => {
                            if (this.theme === 'auto') {
                                this.apply(this.theme);
                            }
                        });
                    }
                }
                this.apply(this.theme);
            },
            labelTheme(){
                if (this.theme === 'auto') {
                    return 'Automático';
                }
                if (this.theme === 'dark') {
                    return 'Escuro';
                }
                return 'Claro';
            },
            setTheme(t){
                this.theme = t;
                localStorage.setItem('trv.site.theme', t);
                this.apply(t);
            },
            apply(t){
                const prefersDark = this.mql ? this.mql.matches : false;
                const dark = t === 'dark' || (t === 'auto' && prefersDark);
                document.documentElement.classList.toggle('dark', dark);
                const meta = document.querySelector('meta[name="theme-color"]');
                if (meta) {
                    meta.setAttribute('content', dark ? '#050608' : '#FFFFFF');
                }
            }
        }
    }
</script>

@stack('scripts')
</body>
</html>

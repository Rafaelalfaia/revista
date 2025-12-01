@extends('site.layout')

@section('title','Revista Trivento · Home')

@section('content')
@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $submissionRoute = $submissionRoute
        ?? (Route::has('autor.submissions.create')
            ? route('autor.submissions.create')
            : (Route::has('login') ? route('login') : '#'));

    $editionsIndexUrl = Route::has('site.editions.index')
        ? route('site.editions.index')
        : (Route::has('admin.editions.index') ? route('admin.editions.index') : '#');

    $categoriesIndexUrl = Route::has('site.categories.index')
        ? route('site.categories.index')
        : '#';

    $authorsUrl = Route::has('site.authors.guidelines')
        ? route('site.authors.guidelines')
        : '#';

    $boardUrl = Route::has('site.editorial.board')
        ? route('site.editorial.board')
        : '#';

    $aboutUrl = Route::has('site.about.journal')
        ? route('site.about.journal')
        : '#';

    $editionShowUrl = function ($edition) {
        if (!$edition) return '#';
        if (Route::has('site.editions.show')) {
            return route('site.editions.show', $edition);
        }
        if (Route::has('admin.editions.show')) {
            return route('admin.editions.show', $edition);
        }
        return '#';
    };

    $submissionShowUrl = function ($submission) {
        if (!$submission) return '#';
        if (Route::has('site.submissions.show')) {
            return route('site.submissions.show', $submission);
        }
        if (Route::has('admin.submissions.show')) {
            return route('admin.submissions.show', $submission);
        }
        return '#';
    };

    $categoryShowUrl = function ($category) {
        if (!$category) return '#';
        if (Route::has('site.categories.show')) {
            return route('site.categories.show', $category);
        }
        if (Route::has('admin.categories.show')) {
            return route('admin.categories.show', $category);
        }
        return '#';
    };

    $heroEditionYear  = $currentEdition?->release_date?->format('Y')
        ?? $currentEdition?->published_at?->format('Y')
        ?? now()->year;

    $heroEditionTitle = $currentEdition->title ?? 'Primeira edição da Revista Trivento';

    $heroEditionLabel = $currentEdition
        ? 'Edição ' . $heroEditionYear
        : 'Primeira edição em preparação';

    $kpiEditions    = $stats['editions']  ?? (($previousEditions->count() ?? 0) + ($currentEdition ? 1 : 0));
    $kpiArticles    = $stats['articles']  ?? ($featuredArticles->count() ?? 0);
    $kpiFirstReview = $stats['avg_first_review_time'] ?? '—';

    $editionsStrip    = $previousEditions   ?? collect();
    $categorySections = $categorySections   ?? collect();
    $topCategories    = $topCategories      ?? collect();
@endphp

<main class="min-h-screen bg-[var(--bg)] text-[var(--text)] pb-24 lg:pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 lg:space-y-10 pt-6">

        {{-- Hero --}}
        <section>
            <div class="rounded-3xl border border-[var(--line)] bg-[var(--panel)] shadow-sm overflow-hidden">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1.6fr),minmax(0,1.1fr)] p-5 sm:p-7 lg:p-8">
                    <div class="flex flex-col gap-4 justify-center">
                        <span class="inline-flex items-center self-start rounded-full border border-[var(--brand)] px-3 py-1 text-[11px] font-semibold tracking-[0.22em] text-[var(--brand)] uppercase">
                            {{ $heroEditionLabel }}
                        </span>

                        <div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-extrabold leading-tight">
                                Revista Trivento
                            </h1>
                            <p class="mt-3 text-sm sm:text-base text-[var(--muted)] max-w-xl">
                                Publicação científica de acesso aberto da Trivento Educação, dedicada à pesquisa,
                                inovação e práticas educacionais.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ $currentEdition ? $editionShowUrl($currentEdition) : '#' }}"
                               class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white bg-[var(--brand)] hover:bg-[var(--brand-700)] shadow-md shadow-rose-500/30 transition">
                                Ler edição atual
                            </a>
                            <a href="{{ $submissionRoute }}"
                               class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold border border-[var(--brand)] text-[var(--brand)] hover:bg-rose-50/70 dark:hover:bg-rose-950/30 transition">
                                Submeter artigo
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center justify-center lg:justify-end">
                        <a href="{{ $currentEdition ? $editionShowUrl($currentEdition) : '#' }}" class="w-full max-w-xs">
                            <div class="relative aspect-[3/4] w-full rounded-3xl overflow-hidden shadow-2xl shadow-rose-500/40 bg-gradient-to-br from-rose-500 via-pink-500 to-fuchsia-500">
                                @if(!empty($currentEditionCover))
                                    <img src="{{ $currentEditionCover }}"
                                         alt="Capa da edição"
                                         class="absolute inset-0 w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br from-rose-500 via-pink-500 to-fuchsia-500"></div>
                                @endif

                                <span class="absolute top-0 right-0 rounded-bl-lg bg-rose-600 text-white text-[11px] font-semibold px-3 py-1">
                                    {{ $currentEdition ? 'Atual' : 'Em preparação' }}
                                </span>

                                <div class="absolute inset-x-0 bottom-0 p-4 sm:p-5 text-white">
                                    <p class="text-[10px] tracking-[0.22em] uppercase mb-1 text-white/80">
                                        {{ $currentEdition ? 'Revista Trivento · Edição ' . $heroEditionYear : 'Revista Trivento' }}
                                    </p>
                                    <p class="text-lg sm:text-xl font-bold leading-snug line-clamp-2">
                                        {{ $heroEditionTitle }}
                                    </p>
                                    @if($currentEdition)
                                        <p class="mt-1 text-xs text-white/80">
                                            {{ $heroEditionYear }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="border-t border-[var(--line)] px-5 sm:px-7 lg:px-8 py-4 bg-[var(--panel-2)]">
                    <div class="flex flex-wrap items-center justify-between gap-4 text-center">
                        <div class="flex-1 min-w-[90px]">
                            <p class="text-2xl sm:text-3xl font-extrabold text-[var(--brand)]">
                                {{ $kpiEditions }}
                            </p>
                            <p class="text-[11px] sm:text-xs text-[var(--muted)]">
                                Edições publicadas
                            </p>
                        </div>
                        <div class="flex-1 min-w-[90px]">
                            <p class="text-2xl sm:text-3xl font-extrabold text-[var(--brand)]">
                                {{ $kpiArticles }}
                            </p>
                            <p class="text-[11px] sm:text-xs text-[var(--muted)]">
                                Artigos publicados
                            </p>
                        </div>
                        <div class="flex-1 min-w-[90px]">
                            <p class="text-2xl sm:text-3xl font-extrabold text-[var(--brand)]">
                                {{ $kpiFirstReview }}
                            </p>
                            <p class="text-[11px] sm:text-xs text-[var(--muted)]">
                                Tempo médio 1ª decisão
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Chamada submissões --}}
        <section>
            <div class="rounded-3xl border border-[var(--line)] bg-[var(--panel-2)] px-5 sm:px-7 py-5 sm:py-6 flex flex-col lg:flex-row items-start lg:items-center gap-4">
                <div class="flex-1">
                    <h2 class="text-sm sm:text-base font-semibold text-rose-700 dark:text-rose-300 mb-1 flex items-center gap-2">
                        <span class="text-xl">📢</span>
                        Chamada aberta para submissões
                    </h2>
                    <p class="text-xs sm:text-sm text-[var(--muted)]">
                        A revista recebe continuamente artigos, relatos de experiência, revisões e outros formatos científicos alinhados ao escopo da Trivento Educação.
                        <span class="font-semibold">Veja as diretrizes para autores e envie seu trabalho.</span>
                    </p>
                </div>
                <a href="{{ $submissionRoute }}"
                   class="inline-flex items-center justify-center rounded-xl px-4 sm:px-5 py-2.5 text-xs sm:text-sm font-semibold border border-[var(--brand)] text-[var(--brand)] bg-[var(--panel)] hover:bg-rose-50/70 dark:hover:bg-rose-950/30 transition w-full lg:w-auto">
                    Submeter artigo
                </a>
            </div>
        </section>

        {{-- Artigos em destaque --}}
        <section x-data="featuredCarousel()">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xl sm:text-2xl font-bold">
                    Artigos em destaque
                </h2>
                @if($featuredArticles->isNotEmpty())
                    <a href="{{ $currentEdition ? $editionShowUrl($currentEdition) : '#' }}"
                       class="text-xs sm:text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-700)]">
                        Ver todos da edição
                    </a>
                @endif
            </div>

            @if($featuredArticles->isEmpty())
                <div class="rounded-2xl border border-dashed border-[var(--line)] bg-[var(--panel-2)] px-4 py-6 text-xs sm:text-sm text-[var(--muted)] text-center">
                    Assim que os editores marcarem publicações como destacadas, elas aparecerão aqui.
                </div>
            @else
                <div class="relative">
                    <div class="flex gap-4 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory scroll-smooth" x-ref="track">
                        @foreach($featuredArticles as $submission)
                            @php
                                $subUrl          = $submissionShowUrl($submission);
                                $primaryCategory = $submission->categories->first();
                                $authorName      = optional($submission->author)->name ?? optional($submission->user)->name;
                                $date            = $submission->published_at ?? $submission->accepted_at ?? $submission->submitted_at ?? $submission->created_at;
                            @endphp
                            <a href="{{ $subUrl }}"
                               class="snap-start shrink-0 w-1/2 sm:w-1/3 lg:w-1/3 xl:w-1/4 rounded-2xl border border-[var(--line)] bg-[var(--panel)] shadow-sm overflow-hidden flex flex-col">
                                <div class="relative h-32 sm:h-36 overflow-hidden">
                                    @if(!empty($currentEditionCover))
                                        <img src="{{ $currentEditionCover }}"
                                             alt="Capa da edição"
                                             class="w-full h-full object-cover opacity-80 transition">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-rose-500 via-pink-500 to-fuchsia-600"></div>
                                    @endif

                                    @if($primaryCategory)
                                        <span class="absolute top-2 left-2 rounded-full bg-black/60 text-[10px] text-white px-2.5 py-1">
                                            {{ $primaryCategory->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="p-4 flex-1 flex flex-col">
                                    <h3 class="text-sm sm:text-base font-semibold leading-snug mb-2 line-clamp-2 hover:text-[var(--brand)] transition">
                                        {{ $submission->title }}
                                    </h3>

                                    @if($authorName || $date)
                                        <div class="text-[11px] text-[var(--muted)] mb-2 space-y-1">
                                            @if($authorName)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--brand)]"></span>
                                                    <span class="truncate">{{ $authorName }}</span>
                                                </div>
                                            @endif
                                            @if($date)
                                                <div class="flex items-center gap-1.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ $date->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <span class="mt-auto inline-flex items-center gap-1 text-[11px] font-semibold text-[var(--brand)]">
                                        Ler publicação
                                        <span aria-hidden="true">→</span>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="hidden sm:flex items-center justify-end gap-2 mt-2">
                        <button type="button"
                                class="h-8 w-8 rounded-full border border-[var(--line)] bg-[var(--panel)] flex items-center justify-center text-xs"
                                @click="prev">
                            ←
                        </button>
                        <button type="button"
                                class="h-8 w-8 rounded-full border border-[var(--line)] bg-[var(--panel)] flex items-center justify-center text-xs"
                                @click="next">
                            →
                        </button>
                    </div>
                </div>
            @endif
        </section>

        {{-- Publicações por área --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xl sm:text-2xl font-bold">
                    Publicações por área
                </h2>
                @if($categorySections->isNotEmpty())
                    <a href="{{ $categoriesIndexUrl }}"
                       class="text-xs sm:text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-700)]">
                        Ver todas as áreas
                    </a>
                @endif
            </div>

            @if($categorySections->isEmpty())
                <p class="text-xs sm:text-sm text-[var(--muted)]">
                    Assim que houver publicações associadas às áreas de conhecimento, elas serão agrupadas aqui.
                </p>
            @else
                <div class="space-y-6">
                    @foreach($categorySections as $category)
                        @php
                            $items = $category->home_submissions ?? collect();
                        @endphp
                        @if($items->isEmpty())
                            @continue
                        @endif

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-100 text-[var(--brand)] text-xs">
                                        {{ mb_substr($category->name,0,1) }}
                                    </span>
                                    <h3 class="text-sm sm:text-base font-semibold">
                                        {{ $category->name }}
                                    </h3>
                                </div>

                                <a href="{{ $categoryShowUrl($category) }}"
                                   class="text-[11px] sm:text-xs font-medium text-[var(--brand)] hover:text-[var(--brand-700)]">
                                    Ver tudo
                                </a>
                            </div>

                            <div class="flex gap-3 overflow-x-auto pb-1 -mx-1 px-1 snap-x snap-mandatory scroll-smooth">
                                @foreach($items as $submission)
                                    @php
                                        $subUrl  = $submissionShowUrl($submission);
                                        $author  = optional($submission->author)->name ?? optional($submission->user)->name;
                                        $date    = $submission->published_at ?? $submission->accepted_at ?? $submission->submitted_at ?? $submission->created_at;
                                    @endphp
                                    <a href="{{ $subUrl }}"
                                       class="snap-start flex-shrink-0 w-64 rounded-2xl border border-[var(--line)] bg-[var(--panel)] shadow-sm px-4 py-3 flex flex-col">
                                        <h4 class="text-sm font-semibold leading-snug mb-1.5 line-clamp-2">
                                            {{ $submission->title }}
                                        </h4>
                                        @if($author)
                                            <p class="text-[11px] text-[var(--muted)] truncate">
                                                {{ $author }}
                                            </p>
                                        @endif
                                        @if($date)
                                            <p class="text-[10px] text-[var(--muted)] mt-0.5">
                                                {{ $date->format('d/m/Y') }}
                                            </p>
                                        @endif
                                        <span class="mt-auto pt-1 text-[11px] font-semibold text-[var(--brand)]">
                                            Ver detalhes →
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Edições anteriores --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl sm:text-2xl font-bold">
                    Edições anteriores
                </h2>
                @if($editionsStrip->isNotEmpty())
                    <a href="{{ $editionsIndexUrl }}"
                       class="text-xs sm:text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-700)]">
                        Ver todas
                    </a>
                @endif
            </div>

            @if($editionsStrip->isEmpty())
                <div class="rounded-2xl border border-dashed border-[var(--line)] bg-[var(--panel-2)] px-4 py-6 text-xs sm:text-sm text-[var(--muted)] text-center">
                    Assim que houver mais de uma edição cadastrada, o arquivo ficará disponível aqui.
                </div>
            @else
                <div class="flex gap-3 sm:gap-4 overflow-x-auto pb-1 -mx-1 px-1 snap-x snap-mandatory scroll-smooth">
                    @foreach($editionsStrip as $edition)
                        @php
                            $year  = $edition->release_date?->format('Y') ?? $edition->published_at?->format('Y') ?? null;
                            $label = $edition->title ?? ($year ? 'Edição ' . $year : 'Edição');
                            $label = Str::limit($label, 40);
                        @endphp
                        <a href="{{ $editionShowUrl($edition) }}"
                           class="snap-start flex-shrink-0 w-32 sm:w-36 rounded-2xl border border-[var(--line)] bg-[var(--panel)] shadow-sm px-3 py-3 text-center hover:border-[var(--brand)] hover:shadow-md transition">
                            <p class="text-lg sm:text-xl font-black mb-1">
                                {{ $year ?? '—' }}
                            </p>
                            <p class="text-[11px] sm:text-xs font-semibold text-[var(--brand)] line-clamp-2">
                                {{ $label }}
                            </p>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Informação extra + áreas principais --}}
        <section class="pb-4">
            <h2 class="text-xl sm:text-2xl font-bold text-center mb-6">
                Áreas de conhecimento e informações-chave
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5 mb-8">
                <div class="rounded-2xl border border-[var(--line)] bg-[var(--panel)] shadow-sm p-5 flex flex-col">
                    <span class="text-[var(--brand)] mb-3 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                            <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                            <path d="M10 9H8"/>
                            <path d="M16 13H8"/>
                            <path d="M16 17H8"/>
                        </svg>
                    </span>
                    <h3 class="text-lg font-semibold mb-1.5">Para autores</h3>
                    <p class="text-xs sm:text-sm text-[var(--muted)] mb-3">
                        Diretrizes de submissão, modelos de artigo e orientações sobre o fluxo editorial da revista.
                    </p>
                    <a href="{{ $authorsUrl }}" class="mt-auto text-[var(--brand)] text-xs sm:text-sm font-semibold inline-flex items-center gap-1">
                        Diretrizes para autores
                        <span>→</span>
                    </a>
                </div>

                <div class="rounded-2xl border border-[var(--line)] bg-[var(--panel)] shadow-sm p-5 flex flex-col">
                    <span class="text-[var(--brand)] mb-3 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    <h3 class="text-lg font-semibold mb-1.5">Conselho editorial</h3>
                    <p class="text-xs sm:text-sm text-[var(--muted)] mb-3">
                        Conheça a equipe de editores e revisores responsáveis pela avaliação dos trabalhos submetidos.
                    </p>
                    <a href="{{ $boardUrl }}" class="mt-auto text-[var(--brand)] text-xs sm:text-sm font-semibold inline-flex items-center gap-1">
                        Ver membros
                        <span>→</span>
                    </a>
                </div>

                <div class="rounded-2xl border border-[var(--line)] bg-[var(--panel)] shadow-sm p-5 flex flex-col">
                    <span class="text-[var(--brand)] mb-3 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </span>
                    <h3 class="text-lg font-semibold mb-1.5">Sobre a revista</h3>
                    <p class="text-xs sm:text-sm text-[var(--muted)] mb-3">
                        Missão, escopo, políticas editoriais e compromisso com o acesso aberto à produção científica.
                    </p>
                    <a href="{{ $aboutUrl }}" class="mt-auto text-[var(--brand)] text-xs sm:text-sm font-semibold inline-flex items-center gap-1">
                        Saiba mais
                        <span>→</span>
                    </a>
                </div>
            </div>

            @if($topCategories->isNotEmpty())
                <div>
                    <h3 class="text-sm sm:text-base font-semibold mb-2">
                        Principais áreas de conhecimento
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($topCategories as $category)
                            <a href="{{ $categoryShowUrl($category) }}"
                               class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500/10 via-fuchsia-500/5 to-sky-500/10 border border-rose-400/40 px-3 py-1.5 text-[10px] sm:text-xs shadow-sm hover:border-rose-400 hover:bg-rose-500/15 transition">
                                @if($category->icon_url)
                                    <img src="{{ $category->icon_url }}"
                                         alt="{{ $category->name }}"
                                         class="h-4 w-4 rounded-full object-cover">
                                @else
                                    <span class="h-1.5 w-1.5 rounded-full bg-[var(--brand)]"></span>
                                @endif
                                <span class="font-medium truncate max-w-[140px] sm:max-w-[180px]">
                                    {{ $category->name }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    </div>
</main>

{{-- Rodapé: só aparece se não estiver instalado como PWA --}}
<footer id="site-footer" class="border-t border-[var(--line)] bg-[var(--panel)]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col md:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-[var(--muted)]">
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
            <a href="https://triventoeducacao.com.br/" target="_blank" rel="noopener"
               class="hover:text-[var(--brand)] transition-colors">
                Site oficial
            </a>
            <span class="hidden sm:inline text-[var(--line)]">•</span>
            <a href="https://www.instagram.com/triventoeducacao/" target="_blank" rel="noopener"
               class="hover:text-[var(--brand)] transition-colors">
                Instagram
            </a>
            <span class="hidden sm:inline text-[var(--line)]">•</span>
            <a href="https://br.linkedin.com/company/trivento-educacao" target="_blank" rel="noopener"
               class="hover:text-[var(--brand)] transition-colors">
                LinkedIn
            </a>
            <span class="hidden sm:inline text-[var(--line)]">•</span>
            <a href="https://www.youtube.com/@triventoeducacao1286" target="_blank" rel="noopener"
               class="hover:text-[var(--brand)] transition-colors">
                YouTube
            </a>
        </div>

        <div class="text-[11px] sm:text-xs text-center md:text-right">
            © 2025 Trivento Educação. Todos os direitos reservados.
        </div>
    </div>
</footer>

@push('scripts')
<script>
    function featuredCarousel() {
        return {
            next() {
                const track = this.$refs.track;
                if (!track) return;
                track.scrollBy({ left: track.clientWidth * 0.8, behavior: 'smooth' });
            },
            prev() {
                const track = this.$refs.track;
                if (!track) return;
                track.scrollBy({ left: -track.clientWidth * 0.8, behavior: 'smooth' });
            },
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const footer = document.getElementById('site-footer');
        if (!footer) return;

        const isStandalone =
            window.matchMedia('(display-mode: standalone)').matches ||
            window.matchMedia('(display-mode: fullscreen)').matches ||
            window.matchMedia('(display-mode: minimal-ui)').matches ||
            window.navigator.standalone === true;

        if (isStandalone) {
            footer.style.display = 'none';
        }
    });
</script>
@endpush
@endsection

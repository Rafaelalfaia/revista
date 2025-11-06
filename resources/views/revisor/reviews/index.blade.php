@extends('console.layout')
@section('title','Minhas revisões')
@section('page.title','Minhas revisões')

@section('content')
  @php
    $q      = $q      ?? request('q','');
    $status = $status ?? request('status');

    $statusMap = [
      'atribuida'          => 'Atribuída',
      'em_revisao'         => 'Em revisão',
      'revisao_solicitada' => 'Correções solicitadas',
      'parecer_enviado'    => 'Parecer enviado',
    ];
  @endphp

  @if (session('ok'))
    <div class="mb-3 rounded-lg border panel p-3 text-sm" style="border-color:var(--line)">{{ session('ok') }}</div>
  @endif

  <form method="GET" class="mb-4 grid gap-2 sm:grid-cols-[1fr_220px_auto_auto]">
    <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Buscar por título"
           class="rounded-lg border panel h-10 px-3 bg-transparent focus:ring-2 focus:ring-rose-500/60"
           style="border-color:var(--line)">
    <select id="status" name="status" class="rounded-lg border panel h-10 px-3 bg-transparent" style="border-color:var(--line)">
      <option value="">Todos os status</option>
      @foreach ($statusMap as $k => $v)
        <option value="{{ $k }}" @selected($status === $k)>{{ $v }}</option>
      @endforeach
    </select>
    <button class="rounded-lg border panel h-10 px-3 text-sm" style="border-color:var(--line)">Filtrar</button>
    @if(strlen($q) || strlen($status))
      <a href="{{ route('revisor.reviews.index') }}" class="rounded-lg border panel h-10 px-3 text-sm inline-flex items-center justify-center" style="border-color:var(--line)">Limpar</a>
    @endif
  </form>

  @if(method_exists($reviews,'count') && $reviews->count())
    <div class="mb-2 text-xs muted">
      Mostrando {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} de {{ $reviews->total() }} resultados.
    </div>
  @endif

  {{-- Mobile --}}
  <div class="md:hidden space-y-3">
    @forelse ($reviews as $rv)
      @php
        $s      = $rv->status;
        $label  = $statusMap[$s] ?? ucfirst(str_replace('_',' ', $s));
        $bg     = match($s) {'em_revisao'=>'var(--brand)','revisao_solicitada'=>'rgba(234,179,8,.18)','parecer_enviado'=>'rgba(16,185,129,.18)',default=>'var(--chip)'};
        $tx     = $s === 'em_revisao' ? '#fff' : 'var(--text)';
        $title  = $rv->submission->title ?? '—';
        $cat    = optional($rv->submission->categories->first())->name;
        $pend   = $blockingCounts[$rv->submission_id] ?? 0;
      @endphp
      <div class="rounded-2xl border panel p-3" style="border-color:var(--line)">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="font-medium truncate" title="{{ $title }}">{{ $title }}</div>
            <div class="text-xs muted">Atualizado {{ $rv->updated_at?->diffForHumans() }}</div>
          </div>
          <div class="flex gap-2 items-center">
            @if($pend>0)
              <span class="px-2 py-0.5 rounded-md text-xs" style="background:rgba(244,63,94,.18);color:var(--text)">{{ $pend }} pendência(s)</span>
            @endif
            <span class="px-2 py-0.5 rounded-md text-xs" style="background:{{ $bg }}; color:{{ $tx }}">{{ $label }}</span>
          </div>
        </div>
        @if($cat)<div class="mt-2 text-xs muted">Categoria: {{ $cat }}</div>@endif
        <div class="mt-3 text-right">
          <a href="{{ route('revisor.reviews.show', $rv) }}" class="inline-flex items-center rounded-lg px-3 h-9 text-sm border panel" style="border-color:var(--line)">Abrir</a>
        </div>
      </div>
    @empty
      <div class="rounded-2xl border panel p-6 text-sm muted text-center" style="border-color:var(--line)">Nenhuma revisão encontrada.</div>
    @endforelse
  </div>

  {{-- Desktop --}}
  <div class="hidden md:block overflow-x-auto rounded-2xl border panel" style="border-color:var(--line)">
    <table class="min-w-full text-sm">
      <thead class="panel-2">
        <tr>
          <th class="text-left p-3">Título</th>
          <th class="text-left p-3">Categoria</th>
          <th class="text-left p-3">Pendências</th>
          <th class="text-left p-3">Status</th>
          <th class="text-left p-3">Atualizado</th>
          <th class="text-right p-3">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($reviews as $rv)
          @php
            $s = $rv->status;
            $label = $statusMap[$s] ?? ucfirst(str_replace('_',' ', $s));
            $bg = match($s) {'em_revisao'=>'var(--brand)','revisao_solicitada'=>'rgba(234,179,8,.18)','parecer_enviado'=>'rgba(16,185,129,.18)',default=>'var(--chip)'};
            $tx = $s === 'em_revisao' ? '#fff' : 'var(--text)';
            $title = $rv->submission->title ?? '—';
            $cat = optional($rv->submission->categories->first())->name;
            $pend = $blockingCounts[$rv->submission_id] ?? 0;
          @endphp
          <tr class="border-t" style="border-color:var(--line)">
            <td class="p-3 max-w-[28rem] truncate" title="{{ $title }}">{{ $title }}</td>
            <td class="p-3">{{ $cat ?? '—' }}</td>
            <td class="p-3">
              @if($pend>0)
                <span class="px-2 py-0.5 rounded-md text-xs" style="background:rgba(244,63,94,.18);color:var(--text)">{{ $pend }}</span>
              @else
                <span class="px-2 py-0.5 rounded-md text-xs" style="background:var(--chip);color:var(--text)">0</span>
              @endif
            </td>
            <td class="p-3">
              <span class="px-2 py-0.5 rounded-md text-xs" style="background:{{ $bg }}; color:{{ $tx }}">{{ $label }}</span>
            </td>
            <td class="p-3" title="{{ $rv->updated_at?->format('d/m/Y H:i') }}">{{ $rv->updated_at?->diffForHumans() }}</td>
            <td class="p-3 text-right">
              <a href="{{ route('revisor.reviews.show', $rv) }}" class="inline-flex items-center rounded-lg px-3 h-9 text-sm border panel" style="border-color:var(--line)">Abrir</a>
            </td>
          </tr>
        @empty
          <tr><td class="p-6 muted" colspan="6">Nenhuma revisão encontrada.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $reviews->withQueryString()->links() }}
  </div>
@endsection

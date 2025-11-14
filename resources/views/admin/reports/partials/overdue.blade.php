@php
  use Illuminate\Support\Carbon;
  $rows = collect($rows ?? []);
  $map  = ['atribuida'=>'Atribuída','em_revisao'=>'Em revisão','revisao_solicitada'=>'Correções solicitadas','parecer_enviado'=>'Parecer enviado'];
@endphp

<style>
  .tbl{width:100%;border-collapse:separate;border-spacing:0}
  .tbl th,.tbl td{padding:.6rem .7rem;border-bottom:1px solid var(--line)}
  .tbl th{font-weight:700;text-align:left}
  .chip{background:var(--soft);border:1px solid var(--line);padding:.15rem .6rem;border-radius:.6rem;font-size:.75rem;white-space:nowrap}
  .muted{color:var(--muted)}
</style>

@if($rows->isEmpty())
  <div class="muted">Nenhuma revisão em atraso</div>
@else
  <div class="overflow-auto">
    <table class="tbl">
      <thead>
        <tr>
          <th>Submissão</th>
          <th>Revisor</th>
          <th>Vencimento</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $r)
          <tr>
            <td class="min-w-[260px]">
              {{ $r->submission->title ?? ('#'.$r->submission_id) }}
            </td>
            <td>{{ $r->reviewer->name ?? '—' }}</td>
            <td class="tabular-nums">
              {{ $r->due_at ? Carbon::parse($r->due_at)->format('d/m/Y H:i') : '—' }}
            </td>
            <td><span class="chip">{{ $map[$r->status] ?? ucfirst(str_replace('_',' ',$r->status)) }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif

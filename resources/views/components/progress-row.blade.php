@props(['label','value'=>0,'max'=>1])
@php
  $pct = ($max > 0) ? round(($value*100)/$max,1) : 0;
@endphp
<div class="space-y-1">
  <div class="flex justify-between text-sm">
    <span>{{ $label }}</span>
    <span class="font-medium">{{ $value }}</span>
  </div>
  <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
    <div class="h-2 bg-rose-500" style="width: {{ $pct }}%"></div>
  </div>
</div>

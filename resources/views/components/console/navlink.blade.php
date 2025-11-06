@props(['href' => '#', 'active' => false])
@php
  $base = 'block rounded-lg px-3 py-2 border border-transparent hover:bg-white/50 dark:hover:bg-white/5';
  $on   = 'text-rose-600 dark:text-rose-300 bg-white/60 dark:bg-white/10';
  $cls  = $base . ($active ? ' '.$on : '');
@endphp
<a href="{{ $href }}" {{ $attributes->merge(['class'=>$cls]) }} @if($active) aria-current="page" @endif>
  {{ $slot }}
</a>

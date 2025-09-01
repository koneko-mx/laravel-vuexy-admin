@props([
  'targets' => null,    // string|array|null  -> ej: 'save,photo' o ['save','photo']
  'blur'    => '[1px]', // genera clase Tailwind: backdrop-blur-[1px]
])

@php
  $targetsAttr = is_array($targets) ? implode(',', $targets) : ($targets ?: null);
@endphp

<div
  class="absolute inset-0 z-10 bg-neutral-500/10 backdrop-blur-{{ $blur }}"
  wire:loading.flex
  wire:loading.class="flex items-center justify-center"
  @if($targetsAttr) wire:target="{{ $targetsAttr }}" @endif
>
  <div class="spinner-border" role="status">
    <span class="visually-hidden">Cargando…</span>
  </div>
</div>

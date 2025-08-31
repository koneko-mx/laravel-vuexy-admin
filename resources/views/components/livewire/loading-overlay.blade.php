@props([
  'target' => null,   // método Livewire: ej. 'save'
  'blur'   => '[1px]', // genera 'backdrop-blur-[1px]'
])

<div
  class="absolute inset-0 z-10 bg-neutral-500/10 backdrop-blur-{{ $blur }}"
  @if($target) wire:target="{{ $target }}" @endif
  wire:loading.flex
  wire:loading.class="flex items-center justify-center"
>
  <div class="spinner-border" role="status">
    <span class="visually-hidden">Cargando…</span>
  </div>
</div>

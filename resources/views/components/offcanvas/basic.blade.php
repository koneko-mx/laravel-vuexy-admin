@php
    use Illuminate\Support\Str;
@endphp

@props([
    'id'         => '',     // ID único para el offcanvas
    'title'      => '',     // Título del offcanvas
    'position'   => 'end',  // Posición: 'start', 'end', 'top', 'bottom'
    'size'       => 'md',   // Tamaño: sm, md, lg, xl
    'backdrop'   => true,   // Si se debe mostrar backdrop
    'wireIgnore' => true,   // Ignorar eventos wire (Livewire)
    'listener'   => '',     // Nombre del listener para reload
    'tagName'    => '',     // Etiqueta del formHelpers
])

@php
    $offcanvasClasses = "offcanvas offcanvas-{$position}";

    $offcanvasSize = match ($size) {
        'sm' => 'offcanvas-sm',
        'lg' => 'offcanvas-lg',
        'xl' => 'offcanvas-xl',
        default => '',
    };

    $helperTag = Str::kebab($tagName);
@endphp

<div
    class="{{ $offcanvasClasses }} {{ $offcanvasSize }}"
    tabindex="-1"
    id="{{ $id }}"
    aria-labelledby="{{ $id }}Label"
    {{ $wireIgnore ? 'wire:ignore.self' : '' }}
    data-bs-backdrop="{{ $backdrop ? 'true' : 'false' }}">

    {{-- HEADER --}}
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">{{ $title }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>

    {{-- BODY --}}
    <div class="offcanvas-body p-4">
        {{ $slot }}
    </div>

    {{-- FOOTER SLOT OPCIONAL --}}
    @if (isset($footer))
        <div class="offcanvas-footer border-top p-3">
            {{ $footer }}
        </div>
    @endif
</div>

@push('page-script')
    <script>
        // Evento para inicializar el formulario cuando se carga la página
        window.addEventListener("DOMContentLoaded", () => {
            const register{{ $tagName }}FormEvents = () => {
                document.addEventListener("refresh-{{ $helperTag }}-offcanvas", () => {
                    window.formHelpers['{{ $helperTag }}'].refresh();
                });

                var myOffcanvas = document.getElementById('{{ $id }}')

                myOffcanvas.addEventListener('show.bs.offcanvas', function () {
                    setTimeout(() => window.formHelpers['{{ $helperTag }}'].reloadOffcanvas(), 20);
                });
            }

            window.formHelpers = window.formHelpers || {};
            window.formHelpers['{{ $helperTag }}'] = new FormCanvasHelper('{{ $id }}', @this);

            register{{ $tagName }}FormEvents();
        });
    </script>
@endpush

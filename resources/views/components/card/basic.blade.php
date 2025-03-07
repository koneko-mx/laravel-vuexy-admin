@props([
    'title' => '',
    'subtitle' => '',
    'image' => null,
    'bgColor' => '', // primary, secondary, success, danger, etc.
    'borderColor' => '', // primary, secondary, etc.
    'textColor' => 'text-dark',
    'shadow' => true, // true / false
    'dropdown' => [], // Opciones para el menú
    'footer' => '',
    'footerClass' => '', // Clase CSS personalizada para el footer
    'footerAlign' => 'start', // Alineación: start, center, end
    'footerButtons' => [], // Botones de acción
    'class' => 'mb-6',
])

@php
    $cardClass = 'card ' . ($shadow ? '' : 'shadow-none') . ' ' . ($bgColor ? "bg-$bgColor text-white" : '') . ' ' . ($borderColor ? "border border-$borderColor" : '') . " $class";
    $footerAlignment = match ($footerAlign) {
        'center' => 'text-center',
        'end' => 'text-end',
        default => 'text-start'
    };
@endphp

<div class="{{ $cardClass }}">
    @if ($image)
        <img class="card-img-top" src="{{ $image }}" alt="Card image">
    @endif

    @if ($title || $subtitle || $dropdown)
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
                @if ($title)
                    <h5 class="mb-1 {{ $textColor }}">{{ $title }}</h5>
                @endif
                @if ($subtitle)
                    <p class="card-subtitle {{ $textColor }}">{{ $subtitle }}</p>
                @endif
            </div>
            @if (!empty($dropdown))
                <div class="dropdown">
                    <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-2" type="button" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @foreach ($dropdown as $item)
                            <a class="dropdown-item" href="{{ $item['href'] ?? 'javascript:void(0);' }}">{{ $item['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    {{-- Footer flexible --}}
    @if ($footer || !empty($footerButtons))
        <div class="card-footer {{ $footerClass }} {{ $footerAlignment }}">
            {{-- Slot de contenido directo en el footer --}}
            @if ($footer)
                {!! $footer !!}
            @endif

            {{-- Generación dinámica de botones --}}
            @foreach ($footerButtons as $button)
                <a href="{{ $button['href'] ?? '#' }}" class="btn btn-{{ $button['type'] ?? 'primary' }} {{ $button['class'] ?? '' }}">
                    {{ $button['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</div>

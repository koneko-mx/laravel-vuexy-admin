@props([
    'buttons' => [], // Lista de botones en formato de array
    'toolbar' => false, // Si es un grupo de botones estilo "toolbar"
    'nesting' => false, // Si hay un grupo anidado con dropdown
    'class' => '', // Clases adicionales
])

@php
    // Determinar si es un toolbar o un grupo simple
    $groupClass = $toolbar ? 'btn-toolbar' : 'btn-group';
@endphp

<div class="{{ $groupClass }} {{ $class }}" role="group" aria-label="Button group">
    @foreach($buttons as $button)
        @if(isset($button['dropdown']))
            {{-- Botón con dropdown anidado --}}
            <div class="btn-group" role="group">
                <button id="btnGroupDrop{{ $loop->index }}" type="button" class="btn {{ $button['variant'] ?? 'btn-outline-secondary' }} dropdown-toggle waves-effect" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @if(isset($button['icon'])) <i class="{{ $button['icon'] }} ti-md"></i> @endif
                    <span class="d-none d-sm-block">{{ $button['label'] ?? 'Dropdown' }}</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $loop->index }}">
                    @foreach($button['dropdown'] as $dropdownItem)
                        <a class="dropdown-item waves-effect" href="{{ $dropdownItem['href'] ?? 'javascript:void(0);' }}">
                            {{ $dropdownItem['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Botón normal o ancla --}}
            @if(isset($button['href']))
                <a href="{{ route($button['href']) }}" class="btn {{ $button['variant'] ?? 'btn-outline-secondary' }} waves-effect">
                    @if(isset($button['icon'])) <i class="{{ $button['icon'] }} ti-md"></i> @endif
                    {{ $button['label'] }}
                </a>
            @else
                <button type="{{ $button['type'] ?? 'button' }}" class="btn {{ $button['variant'] ?? 'btn-outline-secondary' }} waves-effect"
                    {{ $button['disabled'] ?? false ? 'disabled' : '' }}>
                    @if(isset($button['icon'])) <i class="{{ $button['icon'] }} ti-md"></i> @endif
                    {{ $button['label'] }}
                </button>
            @endif
        @endif
    @endforeach
</div>

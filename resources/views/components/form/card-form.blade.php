{{-- resources/views/components/form/card-form.blade.php --}}
@props([
    'id'            => uniqid(),
    'formClass'     => 'mb-4',
    'novalidate'    => true,

    'title'         => '',
    'subtitle'      => '',
    'cardClass'     => 'mb-2 position-relative',
    'textColor'     => 'text-dark',
    'dropdown'      => [],

    'overlay'       => true,
    'overlayTarget' => 'save',
    'overlayBlur'   => '[1px]',

    'linkHref'      => null,
    'linkText'      => null,

    'showActions'   => false,
    'cancelClick'   => 'resetForm',
    'saveLabel'     => 'Guardar cambios',
    'cancelLabel'   => 'Cancelar',
    'loadingText'   => 'Guardando...',
    'actionsAlign'  => 'end', // start|center|end
    // OPCIONAL: controla dónde quiere el form las acciones
    'actionPosition' => 'bottom', // top|bottom|both|none
])

<x-vuexy-admin::form.form
    :id="$id"
    class="form-custom-listener {{ $formClass }}"
    whitOutId
    whitOutMode
    :novalidate="$novalidate"
    :actionPosition="$actionPosition"
    {{ $attributes }}
>
    {{-- >>> Slot "actions" que el form colocará en sus <div class="form-actions"> --}}
    <x-slot:actions>
        @isset($actions)
            {{-- El padre definió acciones custom --}}
            {{ $actions }}
        @elseif($showActions)
            {{-- Acciones por defecto (Save/Cancel) --}}
            <x-vuexy-admin::form.save-cancel
                :saveLabel="$saveLabel"
                :cancelLabel="$cancelLabel"
                :cancelClick="$cancelClick"
                :align="$actionsAlign"
                :loadingText="$loadingText"
            />
        @endisset
    </x-slot:actions>

    <x-vuexy-admin::card.basic
        :title="$title"
        :subtitle="$subtitle"
        :textColor="$textColor"
        :dropdown="$dropdown"
        class="{{ $cardClass }}"
    >
        @if($overlay)
            <x-vuexy-admin::livewire.loading-overlay :target="$overlayTarget" :blur="$overlayBlur" />
        @endif

        @if($linkHref && $linkText)
            <div class="mb-3">
                <a href="{{ $linkHref }}" target="_blank" rel="noopener noreferrer">{{ $linkText }}</a>
            </div>
        @endif

        {{ $slot }}
    </x-vuexy-admin::card.basic>
</x-vuexy-admin::form.form>

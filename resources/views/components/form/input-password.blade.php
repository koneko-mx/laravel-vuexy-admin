@props([
    'uid' => uniqid(),
    'model' => '',
    'label' => 'Contraseña',
    'placeholder' => '············',
    'labelClass' => '',
    'icon' => null,
])

@php
    $livewireModel = $attributes->get('wire:model', $model);
    $name = $attributes->get('name', $livewireModel);
    $inputId = $attributes->get('id', $name . '_' . $uid);

    $errorKey = $livewireModel ?: $name;
    $hasError = $errors->has($errorKey);
    $errorClass = $hasError ? 'is-invalid' : '';
@endphp

<div class="mb-4 fv-row">
    <label class="form-label {{ $labelClass }}" for="{{ $inputId }}">{{ $label }}</label>

    <div class="input-group input-group-merge">
        {{-- Prefijo (opcional) --}}
        @if ($icon)
            <span class="input-group-text">
                <i class="{{ $icon }}"></i>
            </span>
        @endif

        {{-- Input --}}
        <input
            type="password"
            id="{{ $inputId }}"
            name="{{ $name }}"
            wire:model="{{ $livewireModel }}"
            class="form-control {{ $errorClass }}"
            placeholder="{{ $placeholder }}"
            autocomplete="new-password"
            aria-describedby="{{ $inputId }}-aria"
        />

        {{-- Toggle --}}
        <span class="input-group-text cursor-pointer toggle-password" data-target="{{ $inputId }}">
            <i class="ti ti-eye-off password-toggle-icon"></i>
        </span>
    </div>
    @if ($hasError)
        <span class="text-danger">{{ $errors->first($errorKey) }}</span>
    @endif
</div>

@push('page-script')
    <script>
        function attachPasswordToggles() {
            document.querySelectorAll('.toggle-password').forEach(toggle => {
                const alreadyAttached = toggle.getAttribute('data-bound');
                if (alreadyAttached) return;

                toggle.setAttribute('data-bound', 'true');

                toggle.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('.password-toggle-icon');

                    if (input && icon) {
                        const isPassword = input.type === 'password';
                        input.type = isPassword ? 'text' : 'password';
                        icon.classList.toggle('ti-eye', isPassword);
                        icon.classList.toggle('ti-eye-off', !isPassword);
                    }
                });
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            attachPasswordToggles();
        });
    </script>
@endpush

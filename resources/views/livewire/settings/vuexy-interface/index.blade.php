<div x-data>
    <div id="vuexy-interface-index-card" class="form-custom-listener mb-4">
        {{-- Notificaciones --}}
        <div class="notification-container pt-4" wire:ignore></div>

        <div class="row">
            <div class="col-md-6">
                {{-- Tema --}}
                <x-vuexy-admin::card.basic title="Ajustes de tema" class="mb-6">
                    <x-vuexy-admin::form.select :uid="$uniqueId" model="vuexy_myTheme" label="Tema" :options="['theme-default' => 'Tema predeterminado', 'theme-bordered' => 'Tema bordeado', 'theme-semi-dark' => 'Tema semi-oscuro']" />
                    <x-vuexy-admin::form.select :uid="$uniqueId" model="vuexy_myStyle" label="Estilo" :options="['light' => 'Claro', 'dark' => 'Oscuro', 'system' => 'Modo del sistema']" />
                </x-vuexy-admin::card.basic>

                {{-- Diseño --}}
                <x-vuexy-admin::card.basic title="Ajustes de diseño" class="mb-6">
                    <x-vuexy-admin::form.select :uid="$uniqueId" model="vuexy_authViewMode" label="Modo de vista de autenticación" :options="['cover' => 'Pantalla completa', 'basic' => 'Básico']" />
                    <x-vuexy-admin::form.select :uid="$uniqueId" model="vuexy_contentLayout" label="Ancho predeterminado" :options="['compact' => 'Compacto', 'wide' => 'Ancho completo']" />
                    <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="vuexy_footerFixed" label="Fijar pie de página" switch />
                </x-vuexy-admin::card.basic>
            </div>
            <div class="col-md-6">
                {{-- Ajustes de menú y barra superior --}}
                <x-vuexy-admin::card.basic title="Ajustes menú y barra superior" class="mb-6">
                    {{-- Diseño (Layout) --}}
                    <x-vuexy-admin::form.select :uid="$uniqueId" model="vuexy_myLayout" label="Diseño de menú" :options="['vertical' => 'Vertical', 'horizontal' => 'Horizontal']" />
                    {{-- Horizontal layout --}}
                    <div x-show="$wire.vuexy_myLayout === 'horizontal'" x-cloak x-transition>
                        <x-vuexy-admin::form.select :uid="$uniqueId" model="vuexy_headerType" label="Tipo de barra superior" :options="['static' => 'Estático', 'fixed' => 'Fijo']" />
                    </div>
                    {{-- Vertical layout --}}
                    <div x-show="$wire.vuexy_myLayout === 'vertical'" x-cloak x-transition>
                        <x-vuexy-admin::form.select :uid="$uniqueId" model="vuexy_navbarType" label="Tipo de barra de navegación" :options="['sticky' => 'Fija', 'static' => 'Estática', 'hidden' => 'Oculta']" />
                    </div>
                    {{-- Personalizador activo --}}
                    <div x-show="$wire.vuexy_myLayout === 'horizontal'" x-cloak x-transition>
                        <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="vuexy_showDropdownOnHover" label="Mostrar desplegable al pasar el mouse" switch />
                    </div>
                    {{-- Opciones para diseño vertical --}}
                    <div x-show="$wire.vuexy_myLayout === 'vertical'" x-cloak x-transition>
                        <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="vuexy_menuFixed" label="Menú fijo" switch />
                        <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="vuexy_menuCollapsed" label="Menú colapsado" switch />
                    </div>
                </x-vuexy-admin::card.basic>

                {{-- Atajos --}}
                <div x-show="$wire.vuexy_myLayout === 'horizontal' || $wire.vuexy_navbarType !== 'hidden'" x-cloak x-transition>
                    <x-vuexy-admin::card.basic title="Atajos" class="mb-6">
                        <x-vuexy-admin::form.input type="number" :uid="$uniqueId" model="vuexy_maxQuickLinks" label="Máximo de enlaces rápidos" min="2" max="20" helperText="Selecciona un valor entre 2 y 20." />
                    </x-vuexy-admin::card.basic>
                </div>

                {{-- Personalizador de plantilla --}}
                <x-vuexy-admin::card.basic title="Personalizador de plantilla" class="mb-6">
                    <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="vuexy_hasCustomizer" label="Habilitar personalizador de plantilla" switch />
                    <div x-show="$wire.vuexy_hasCustomizer" x-cloak x-transition>
                        <x-vuexy-admin::form.checkbox :uid="$uniqueId" model="vuexy_displayCustomizer" label="Mostrar personalizador de plantilla" switch />
                    </div>
                </x-vuexy-admin::card.basic>
            </div>
        </div>
        {{-- Acciones --}}
        <div class="row">
            <div class="col-lg-12 text-end">
                <x-vuexy-admin::button.basic wire:click="save" disabled variant="primary" icon="ti ti-check" label="Aplicar cambios" class="btn-save mb-2 mx-2" size="sm" waves />
                <x-vuexy-admin::button.basic wire:click="loadForm" disabled variant="secondary" icon="ti ti-rotate-2" label="Cancelar" class="btn-cancel mb-2 mx-2" size="sm" waves />
            </div>
            <div class="col-lg-12 text-end">
                <x-vuexy-admin::button.basic wire:click="clearCustomConfig" variant="success" icon="ti ti-adjustments-cog" label="Restaurar valores predeterminados" class="btn-reset mb-2 mx-2" size="sm" waves />
            </div>
        </div>
    </div>
</div>

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('refreshAndNotify', event => {
                const notification = {
                    type: event.type || 'success',
                    message: event.message || 'Configuraciones guardadas exitosamente.',
                    target: event.target || '#vuexy-interface-index-card .notification-container',
                    delay: event.delay || 6000
                };

                // Guardar notificación en localStorage
                localStorage.setItem('vuexy_notification', JSON.stringify(notification));

                // Refrescar la página
                window.location.reload();
            });
        });
    </script>
@endpush
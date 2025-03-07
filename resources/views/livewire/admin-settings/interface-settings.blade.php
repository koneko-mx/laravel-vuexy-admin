<div>
    <div class="form-custom-listener" id="interface-settings-card">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Ajustes menú y barra superior</h5>

                {{-- Diseño (Layout) --}}
                <div class="mb-4">
                    <label for="vuexy_myLayout" class="form-label">Diseño</label>
                    <select id="vuexy_myLayout" class="form-select" wire:model="vuexy_myLayout">
                        <option value="vertical">Vertical</option>
                        <option value="horizontal">Horizontal</option>
                    </select>
                </div>

                {{-- Campos exclusivos para diseño Horizontal --}}
                <div x-show="$wire.vuexy_myLayout === 'horizontal'" x-transition>
                    <div class="mb-4">
                        <label for="vuexy_headerType" class="form-label">Tipo de barra superior</label>
                        <select id="vuexy_headerType" class="form-select" wire:model="vuexy_headerType">
                            <option value="static">Estático</option>
                            <option value="fixed">Fijo</option>
                        </select>
                    </div>
                </div>

                {{-- Campos exclusivos para diseño Vertical --}}
                <div x-show="$wire.vuexy_myLayout === 'vertical'" x-transition>
                    <div class="mb-4">
                        <label for="vuexy_navbarType" class="form-label">Tipo de barra de navegación</label>
                        <select id="vuexy_navbarType" class="form-select" wire:model="vuexy_navbarType">
                            <option value="sticky">Fija</option>
                            <option value="static">Estática</option>
                            <option value="hidden">Oculta</option>
                        </select>
                    </div>
                </div>

                <div x-show="$wire.vuexy_hasCustomizer" x-transition>
                    <div x-show="$wire.vuexy_myLayout === 'horizontal'" x-transition>
                        <div class="mb-3">
                            <x-vuexy-admin::form.checkbox
                                wire:model='vuexy_showDropdownOnHover'
                                parent_class='form-switch'>
                                Mostrar desplegable al pasar el mouse
                            </x-vuexy-admin::form.checkbox>
                        </div>
                    </div>
                </div>

                {{-- Campos exclusivos para diseño Vertical --}}
                <div x-show="$wire.vuexy_myLayout === 'vertical'" x-transition>
                    <div class="mb-3">
                        <x-vuexy-admin::form.checkbox
                            wire:model='vuexy_menuFixed'
                            parent_class='form-switch'>
                            Menú fijo
                        </x-vuexy-admin::form.checkbox>
                    </div>
                    <div class="mb-3">
                        <x-vuexy-admin::form.checkbox
                            wire:model='vuexy_menuCollapsed'
                            parent_class='form-switch'>
                            Menú colapsado
                        </x-vuexy-admin::form.checkbox>
                    </div>
                </div>

                <hr>

                {{-- Habilitar UI Customizer --}}
                <div class="mb-3">
                    <x-vuexy-admin::form.checkbox
                        wire:model='vuexy_hasCustomizer'
                        parent_class='form-switch'>
                        Habilitar personalizador de plantilla
                    </x-vuexy-admin::form.checkbox>
                </div>

                <div x-show="$wire.vuexy_hasCustomizer" x-transition>
                    <div class="mb-3">
                        <x-vuexy-admin::form.checkbox
                            wire:model='vuexy_displayCustomizer'
                            parent_class='form-switch'>
                            Mostrar personalizador de plantilla
                        </x-vuexy-admin::form.checkbox>
                    </div>
                </div>

                {{-- Máximo de Enlaces Rápidos --}}
                <div x-show="$wire.vuexy_myLayout === 'horizontal' || $wire.vuexy_navbarType !== 'hidden'" x-transition>
                    <hr>
                    <div class="mb-4">
                        <label for="vuexy_maxQuickLinks" class="form-label">Máximo de enlaces rápidos</label>
                        <input type="number" id="vuexy_maxQuickLinks" class="form-control" wire:model="vuexy_maxQuickLinks" min="2" max="20">
                        <p class="text-muted">Selecciona un valor entre 2 y 20.</p>
                        @error('vuexy_maxQuickLinks') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr>

                <h5 class="card-title mt-6">Ajustes de tema</h5>

                {{-- Tema --}}
                <div class="mb-4">
                    <label for="vuexy_myTheme" class="form-label">Tema</label>
                    <select id="vuexy_myTheme" class="form-select" wire:model="vuexy_myTheme">
                        <option value="theme-default">Tema predeterminado</option>
                        <option value="theme-bordered">Tema bordeado</option>
                        <option value="theme-semi-dark">Tema semi-oscuro</option>
                    </select>
                </div>

                {{-- Estilo --}}
                <div class="mb-4">
                    <label for="vuexy_myStyle" class="form-label">Estilo</label>
                    <select id="vuexy_myStyle" class="form-select" wire:model="vuexy_myStyle">
                        <option value="light">Claro</option>
                        <option value="dark">Oscuro</option>
                        <option value="system">Modo del sistema</option>
                    </select>
                </div>

                <hr>

                <h5 class="card-title mt-6">Ajustes de diseño</h5>

                {{-- Vista de Autenticación --}}
                <div class="mb-4">
                    <label for="vuexy_authViewMode" class="form-label">Modo de vista de autenticación</label>
                    <select id="vuexy_authViewMode" class="form-select" wire:model="vuexy_authViewMode">
                        <option value="cover">Pantalla completa</option>
                        <option value="basic">Básico</option>
                    </select>
                </div>

                {{-- Diseño del Contenido --}}
                <div class="mb-4">
                    <label for="vuexy_contentLayout" class="form-label">Diseño del contenido</label>
                    <select id="vuexy_contentLayout" class="form-select" wire:model="vuexy_contentLayout">
                        <option value="compact">Compacto</option>
                        <option value="wide">Ancho</option>
                    </select>
                </div>

                {{-- Pie de Página Fijo --}}
                <div class="mb-3">
                    <x-vuexy-admin::form.checkbox
                        wire:model='vuexy_footerFixed'
                        parent_class='form-switch'>
                        Pie de página fijo
                    </x-vuexy-admin::form.checkbox>
                </div>

                {{-- Botones --}}
                <div class="row mt-6">
                    <div class="col-lg-12 text-end">
                    </div>
                </div>
            </div>
        </div>
        <div>
            {{-- Botones --}}
            <div class="row my-4">
                <div class="col-lg-12 text-end">
                    <button
                        type="button"
                        wire:click="save"
                        disabled
                        class="btn btn-primary btn-save btn-sm mt-2 mr-2 waves-effect waves-light">
                        <i class="ti ti-device-floppy me-1"></i>
                        Guardar cambios
                    </button>
                    <button
                        type="button"
                        wire:click="loadSettings"
                        disabled
                        class="btn btn-secondary btn-cancel btn-sm mt-2 mr-2 waves-effect waves-light">
                        <i class="ti ti-rotate-2 me-1"></i>
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="clearCustomConfig"
                        class="btn btn-success btn-reset btn-sm mt-2 mr-2 waves-effect waves-light">
                        <i class="ti ti-adjustments-cog me-1"></i>
                        Restaurar valores predeterminados
                    </button>
                </div>
            </div>
            {{-- Notifications --}}
            <div class="notification-container" wire:ignore></div>
        </div>
    </div>
</div>

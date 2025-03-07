<div>
    <div id="cache-functions-card">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Resumen de Caché y Funcionalidades</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Detalles</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Caché General --}}
                            <tr>
                                <td><strong>Caché general</strong></td>
                                <td class="text-center">
                                    <span class="{{ is_numeric($cacheCounts['general']) && $cacheCounts['general'] > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ is_numeric($cacheCounts['general']) ? $cacheCounts['general'] : 'Error' }}
                                    </span>
                                </td>
                                <td>Elementos almacenados</td>
                                <td>
                                    <button
                                        class="btn btn-primary btn-sm my-2 mr-2"
                                        wire:click="clearLaravelCache"
                                        {{ !is_numeric($cacheCounts['general']) || !$cacheCounts['general'] ? 'disabled' : '' }}
                                        data-loading-text="Eliminando caché...">
                                        Elimina caché de aplicación
                                    </button>
                                </td>
                            </tr>

                            {{-- Configuración --}}
                            <tr>
                                <td><strong>Configuración</strong></td>
                                <td class="text-center">
                                    <span class="{{ $cacheCounts['config'] ? 'text-success' : 'text-danger' }}">
                                        {{ $cacheCounts['config'] ? 'Habilitada' : 'No habilitada' }}
                                    </span>
                                </td>
                                <td>{{ $cacheCounts['config'] ? 'Caché de configuración activa' : 'No se encontró caché de configuración' }}</td>
                                <td>
                                    <button
                                        class="btn btn-secondary btn-sm my-2 mr-2"
                                        wire:click="clearConfigCache"
                                        {{ !$cacheCounts['config'] ? 'disabled' : '' }}
                                        data-loading-text="Eliminando caché...">
                                        Eliminar caché de configuración
                                    </button>
                                    <button
                                        class="btn btn-success btn-config-cache btn-sm my-2 mr-2"
                                        data-loading-text="Generando caché...">
                                        Generar caché de configuración
                                    </button>
                                </td>
                            </tr>

                            {{-- Rutas --}}
                            <tr>
                                <td><strong>Rutas</strong></td>
                                <td class="text-center">
                                    <span class="{{ $cacheCounts['routes'] ? 'text-success' : 'text-danger' }}">
                                        {{ $cacheCounts['routes'] ? 'Habilitada' : 'No habilitada' }}
                                    </span>
                                </td>
                                <td>{{ $cacheCounts['routes'] ? 'Caché de rutas activa' : 'No se encontró caché de rutas' }}</td>
                                <td>
                                    <button
                                        class="btn btn-secondary btn-sm my-2 mr-2"
                                        wire:click="clearRouteCache"
                                        {{ !$cacheCounts['routes'] ? 'disabled' : '' }}
                                        data-loading-text="Eliminando caché...">
                                        Eliminar caché de rutas
                                    </button>
                                    <button
                                        class="btn btn-success btn-cache-routes btn-sm my-2 mr-2"
                                        data-loading-text="Generando caché...">
                                        Generar caché de rutas
                                    </button>
                                </td>
                            </tr>

                            {{-- Vistas --}}
                            <tr>
                                <td><strong>Vistas</strong></td>
                                <td class="text-center">
                                    <span class="{{ $cacheCounts['views'] > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $cacheCounts['views'] }}
                                    </span>
                                </td>
                                <td>Vistas compiladas en el sistema</td>
                                <td>
                                    <button
                                        class="btn btn-secondary btn-sm my-2 mr-2"
                                        wire:click="clearViewCache"
                                        {{ !$cacheCounts['views'] ? 'disabled' : '' }}
                                        data-loading-text="Eliminando caché...">
                                        Eliminar caché de vistas
                                    </button>
                                    <button
                                    class="btn btn-success btn-sm my-2 mr-2"
                                    wire:click="cacheViews"
                                        data-loading-text="Generando caché...">
                                        Generar caché de vistas
                                    </button>
                                </td>
                            </tr>

                            {{-- Eventos --}}
                            <tr>
                                <td><strong>Eventos</strong></td>
                                <td class="text-center">
                                    <span class="{{ $cacheCounts['events'] > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $cacheCounts['events'] ? 'Habilitada' : 'No habilitada' }}
                                    </span>
                                </td>

                                <td>{{ $cacheCounts['events'] ? 'Caché de eventos activa' : 'No se encontró caché de eventos' }}</td>
                                <td>
                                    <button
                                        class="btn btn-secondary btn-sm my-2 mr-2"
                                        wire:click="clearEventCache"
                                        {{ !$cacheCounts['events'] ? 'disabled' : '' }}
                                        data-loading-text="Eliminando caché...">
                                        Eliminar caché de eventos
                                    </button>
                                    <button class="btn btn-success btn-sm my-2 mr-2"
                                        wire:click="cacheEvents"
                                        data-loading-text="Generando caché...">
                                        Generar caché de eventos
                                    </button>
                                </td>
                            </tr>

                            {{-- Optimización --}}
                            <tr>
                                <td><strong>Optimización</strong></td>
                                <td class="text-center">N/A</td>
                                <td>Eliminación de cacde de archivos optimizados, eventos, compilados, configuración, rutas y vistas.</td>
                                <td>
                                    <button
                                        class="btn btn-secondary btn-sm my-2 mr-2"
                                        wire:click="optimizeClear"
                                        data-loading-text="Eliminando caché...">
                                        Elimina archivos optimizados
                                    </button>
                                </td>
                            </tr>

                            {{-- Resets de Autenticación --}}
                            <tr>
                                <td><strong>Roles y permisos</strong></td>
                                <td class="text-center">N/A</td>
                                <td>Gestión de roles y permisos (Spatie Permission)</td>
                                <td>
                                    <button
                                        class="btn btn-secondary btn-sm my-2 mr-2"
                                        wire:click="resetPermissionCache"
                                        data-loading-text="Eliminando caché...">
                                        Eliminar caché de permisos
                                    </button>
                                </td>
                            </tr>

                            {{-- Tokens de restablecimiento --}}
                            <tr>
                                <td><strong>Tokens de restablecimiento</strong></td>
                                <td class="text-center">N/A</td>
                                <td>Eliminación de tokens de restablecimiento</td>
                                <td>
                                    <button
                                        class="btn btn-secondary btn-sm my-2 mr-2"
                                        wire:click="clearResetTokens"
                                        data-loading-text="Eliminando caché...">
                                        Eliminar tokens de restablecimiento
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            {{-- Botones --}}
            <div class="row my-4">
                <div class="col-lg-12 text-end">
                    <button
                        class="btn btn-secondary btn-sm mt-2 mr-2 waves-effect waves-light"
                        wire:click="reloadCacheStats"
                        data-loading-text="Actualizando...">
                        Actualizar
                    </button>
                </div>
            </div>
            {{-- Notifications --}}
            <div class="notification-container" wire:ignore></div>
        </div>
    </div>
</div>

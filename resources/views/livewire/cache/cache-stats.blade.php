<div>
    <div class="form-custom-listener" id="cache-stats-card">
        {{-- Form Card --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Gestión de Caché</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td><strong>Driver</strong></td>
                                <td>{{ $cacheConfig['cache']['default'] }}</td>
                            </tr>
                            @if(in_array($cacheConfig['cache']['default'], ['database', 'memcached', 'redis']))
                                <tr>
                                    <td><strong>Versión</strong></td>
                                    <td>
                                        @if($cacheConfig['cache']['default'] == 'database')
                                            {{ $cacheConfig['driver'][$cacheConfig['database']['default']]['version'] }}
                                        @else
                                            {{ $cacheConfig['driver'][$cacheConfig['cache']['default']]['version'] }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Servidor</strong></td>
                                    <td>{{ $cacheConfig['cache']['host'] }}</td>
                                </tr>
                            @endif

                            @if(in_array($cacheConfig['cache']['default'], ['database', 'redis']))
                                <tr>
                                    <td><strong>Base de datos</strong></td>
                                    <td>
                                        @if ($cacheConfig['cache']['default'] == 'database')
                                            {{ $cacheConfig['database']['connections'][$cacheConfig['database']['default']]['database'] }}
                                        @else
                                            {{ $cacheConfig['database']['redis']['cache']['database'] }}
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @if(in_array($cacheConfig['cache']['default'], ['database', 'memcached', 'redis']))
                                <tr>
                                    <td><strong>Prefijo</strong></td>
                                    <td>{{ $cacheConfig['cache']['prefix'] }}</td>
                                </tr>
                            @endif

                            @if($cacheConfig['cache']['default'] == 'file')
                                <tr>
                                    <td><strong>Ubicación de la Caché</strong></td>
                                    <td>{{ $cacheConfig['cache']['stores']['file']['path'] }}</td>
                                </tr>
                            @endif

                            @if($cacheConfig['cache']['default'] == 'database')
                                <tr>
                                    <td><strong>Tabla de Caché</strong></td>
                                    <td>{{ $cacheConfig['cache']['stores']['database']['table'] }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td><strong>Cantidad de Elementos</strong></td>
                                <td>{{ $cacheStats['item_count'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Espacio Utilizado</strong></td>
                                <td>{{ $cacheStats['memory_usage'] }}</td>
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
                    @if($cacheConfig['cache']['default'] != 'memcached')
                        <button
                            type="button"
                            wire:click="clearCache"
                            class="btn btn-danger btn-clear-cache btn-sm mt-2 mr-2 waves-effect waves-light"
                            data-loading-text="Eliminando Caché...">
                            Eliminar Caché
                        </button>
                    @endif
                    <button
                        type="button"
                        wire:click="reloadCacheStats"
                        class="btn btn-secondary btn-reload-cache-stats btn-sm mt-2 mr-2 waves-effect waves-light"
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

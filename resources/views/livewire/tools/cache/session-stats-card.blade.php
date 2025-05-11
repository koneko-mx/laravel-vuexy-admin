<div>
    <div id="session-stats-card" class="form-custom-listener mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Configuraciones de Sesiones</h5>
                <div class="">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Driver</strong></td>
                                    <td>{{ $cacheConfig['session']['driver'] }}</td>
                                </tr>
                                @if(in_array($cacheConfig['session']['driver'], ['database', 'memcached', 'redis']))
                                    <tr>
                                        <td><strong>Versión</strong></td>
                                        <td>
                                            @if($cacheConfig['session']['driver'] == 'database')
                                                {{ $cacheConfig['driver'][$cacheConfig['database']['default']]['version'] }}
                                            @else
                                                {{ $cacheConfig['driver'][$cacheConfig['session']['driver']]['version'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Servidor</strong></td>
                                        <td>{{ $cacheConfig['session']['host'] }}</td>
                                    </tr>
                                @endif

                                @if(in_array($cacheConfig['session']['driver'], ['database', 'redis']))
                                    <tr>
                                        <td><strong>Base de datos</strong></td>
                                        <td>{{ $cacheConfig['session']['database'] }}</td>
                                    </tr>
                                @endif
                                @if($cacheConfig['session']['driver'] == 'database')
                                    <tr>
                                        <td><strong>Tabla de sessiones</strong></td>
                                        <td>{{ $cacheConfig['session']['table'] }}</td>
                                    </tr>
                                @endif
                                @if ($cacheConfig['session']['driver'] === 'file')
                                    <tr>
                                        <td><strong>Ubicación de las sesiones</strong></td>
                                        <td>{{ $cacheConfig['session']['files'] }}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <td><strong>Tiempo de vida (Minutos)</strong></td>
                                    <td>{{ $cacheConfig['session']['lifetime'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Encriptación habilitada</strong></td>
                                    <td>{{ $cacheConfig['session']['encrypt'] ? 'Sí' : 'No' }}</td>
                                </tr>

                                {{-- Mostrar solo si el driver utiliza cookies --}}
                                @if (in_array($cacheConfig['session']['driver'], ['cookie', 'database', 'redis']))
                                    <tr>
                                        <td><strong>Nombre de la cookie</strong></td>
                                        <td>{{ $cacheConfig['session']['cookie'] ?? 'No especificado' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Path de la Cookie</strong></td>
                                        <td>{{ $cacheConfig['session']['path'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dominio de las sesiones</strong></td>
                                        <td>{{ $cacheConfig['session']['domain'] ?? 'No especificado' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cookies seguras</strong></td>
                                        <td>{{ $cacheConfig['session']['secure'] ? 'Sí' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cookies solo HTTPS</strong></td>
                                        <td>{{ $cacheConfig['session']['http_only'] ? 'Sí' : 'No' }}</td>
                                    </tr>
                                @endif
                                @if($cacheConfig['session']['driver'] != 'memcached')
                                    <tr>
                                        <td><strong>Sesiones</strong></td>
                                        <td>{{ $sessionStats['session_count'] }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if($cacheConfig['session']['driver'] != 'memcached')
            <div>
                {{-- Botones --}}
                <div class="row my-4">
                    <div class="col-lg-12 text-end">
                        @if($cacheConfig['cache']['default'] != 'memcached')
                            <button
                                class="btn btn-danger btn-clear-cache btn-sm mt-2 mr-2 waves-effect waves-light"
                                wire:click="clearSessions"
                                {{ $sessionStats['session_count']? '': 'disabled' }}
                                data-loading-text="Eliminando Sesiones...">
                                Eliminar Sesiones
                            </button>
                        @endif
                        <button
                            class="btn btn-secondary btn-reload-cache-stats btn-sm mt-2 mr-2 waves-effect waves-light"
                            wire:click="reloadSessionStats"
                            data-loading-text="Actualizando...">
                            Actualizar
                        </button>
                    </div>
                </div>
                {{-- Notifications --}}
                <div class="notification-container pt-4" wire:ignore></div>
            </div>
        @endif
    </div>
</div>

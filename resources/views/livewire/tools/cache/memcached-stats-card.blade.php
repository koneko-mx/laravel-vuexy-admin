<div>
    <div id="memcached-stats-card" class="form-custom-listener mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Estadísticas de Memcached</h5>
                @foreach ($memcachedStats as $stat)
                    <table class="table table-bordered table-sm mb-2">
                        <tbody>
                            <tr>
                                <td><strong>Versión de Memcached</strong></td>
                                <td>{{ $stat['version'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Libevent</strong></td>
                                <td>{{ $stat['libevent'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Servidor</strong></td>
                                <td>{{ $stat['server'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Puerto TCP</strong></td>
                                <td>{{ $stat['tcp_port'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Conexiones máximas</strong></td>
                                <td>{{ $stat['max_connections'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Conexiones totales</strong></td>
                                <td>{{ $stat['total_connections'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Conexiones rechazadas</strong></td>
                                <td>{{ $stat['rejected_connections'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Memoria máxima</strong></td>
                                <td>{{ number_format($stat['limit_maxbytes'] / 1024 / 1024, 2) }} MB</td>
                            </tr>
                            <tr>
                                <td><strong>Comandos GET ejecutados</strong></td>
                                <td>{{ $stat['cmd_get'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Comandos SET ejecutados</strong></td>
                                <td>{{ $stat['cmd_set'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>GET exitosos</strong></td>
                                <td>{{ $stat['get_hits'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>GET fallidos</strong></td>
                                <td>{{ $stat['get_misses'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Claves expulsadas</strong></td>
                                <td>{{ $stat['evictions'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Megabytes leídos</strong></td>
                                <td>{{ number_format($stat['bytes_read'] / 1024 / 1024, 2) }} MB</td>
                            </tr>
                            <tr>
                                <td><strong>Megabytes escritos</strong></td>
                                <td>{{ number_format($stat['bytes_written'] / 1024 / 1024, 2) }} MB</td>
                            </tr>
                            <tr>
                                <td><strong>Total de objetos</strong></td>
                                <td>{{ $stat['total_items'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td><strong>Claves almacenadas</strong></td>
                                <td>{{ $stat['curr_items'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Memoria usada</strong></td>
                                <td>
                                    <span class="{{ ($stat['bytes'] / $stat['limit_maxbytes']) > 0.8 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($stat['bytes'] / 1024 / 1024, 2) }} MB
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tiempo de actividad</strong></td>
                                <td>{{ gmdate('H\h i\m s\s', $stat['uptime']) }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
        <div>
            {{-- Botones --}}
            <div class="row my-4">
                <div class="col-12 text-end mb-4">
                    <button
                        class="btn btn-danger btn-clear-cache btn-sm mt-2 mr-2 waves-effect waves-light"
                        wire:click="clearCache"
                        data-loading-text="Eliminando Caché de Memcached..."
                        {{ $stat['curr_items']? '': 'disabled' }}>
                        Eliminar Caché de Memcached
                    </button>
                    <button
                        class="btn btn-secondary btn-reload-cache-stats btn-sm mt-2 mr-2 waves-effect waves-light"
                        wire:click="reloadCacheStats"
                        data-loading-text="Actualizando...">
                        Actualizar
                    </button>
                </div>
            </div>
            {{-- Notifications --}}
            <div class="notification-container mb-4" wire:ignore></div>
        </div>
    </div>
</div>

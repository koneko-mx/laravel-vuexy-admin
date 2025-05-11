<div>
    <div id="redis-stats-card" class="form-custom-listener mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Estadísticas de Redis</h5>
                <div class="">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-2">
                            <tbody>
                                <tr>
                                    <td><strong>Versión de Redis</strong></td>
                                    <td>{{ $redisStats['redis_version'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Servidor</strong></td>
                                    <td>{{ $redisStats['server'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Puerto TCP</strong></td>
                                    <td>{{ $redisStats['tcp_port'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Clientes conectados</strong></td>
                                    <td>{{ $redisStats['connected_clients'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Clientes bloqueados</strong></td>
                                    <td>{{ $redisStats['blocked_clients'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pico máximo de memoria utilizada</strong></td>
                                    <td>
                                        @if ($redisStats['maxmemory'] > 0)
                                            {{-- Usar maxmemory si está configurado --}}
                                            <span class="{{ ($redisStats['used_memory_peak'] / $redisStats['maxmemory']) > 0.8 ? 'text-warning' : 'text-success' }}">
                                                {{ $redisStats['used_memory_peak_human'] }}
                                            </span>
                                        @else
                                            {{-- Usar total_system_memory si maxmemory no está configurado --}}
                                            <span class="{{ ($redisStats['used_memory_peak'] / $redisStats['total_system_memory']) > 0.8 ? 'text-warning' : 'text-success' }}">
                                                {{ $redisStats['used_memory_peak_human'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Memoria total del sistema</strong></td>
                                    <td>{{ $redisStats['total_system_memory_human'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Límite máximo de memoria</strong></td>
                                    <td>
                                        @if ($redisStats['maxmemory'] > 0)
                                            {{ $redisStats['maxmemory_human'] }}
                                        @else
                                            <span class="text-info">Sin límite configurado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total de conexiones recibidas</strong></td>
                                    <td>{{ $redisStats['total_connections_received'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total de comandos procesados</strong></td>
                                    <td>{{ $redisStats['total_commands_processed'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Política de uso de memoria</strong></td>
                                    <td>{{ $redisStats['maxmemory_policy'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rol del servidor</strong></td>
                                    <td>{{ $redisStats['role'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Claves almacenadas</strong></td>
                                    <td>{{ $redisStats['keys'] }}</td>
                                </tr>
                                @isset ($redisStats['databases']['default']['database'])
                                <tr>
                                    <td><strong>Base de datos general de Redis</strong></td>
                                    <td>{{ $redisStats['databases']['default']['database'] }}</td>
                                </tr>
                                @endisset
                                @isset ($redisStats['databases']['cache']['database'])
                                <tr>
                                    <td><strong>Base de datos de caché</strong></td>
                                    <td>{{ $redisStats['databases']['cache']['database'] }}</td>
                                </tr>
                                @endisset
                                @isset ($redisStats['databases']['sessions']['database'])
                                <tr>
                                    <td><strong>Base de datos de sesiones</strong></td>
                                    <td>{{ $redisStats['databases']['sessions']['database'] }}</td>
                                </tr>
                                @endisset
                                <tr>
                                    <td><strong>Memoria usada</strong></td>
                                    <td>
                                        @if ($redisStats['maxmemory'] > 0)
                                            {{-- Usar maxmemory si está configurado --}}
                                            <span class="{{ ($redisStats['used_memory'] / $redisStats['maxmemory']) > 0.8 ? 'text-danger' : 'text-success' }}">
                                                {{ $redisStats['used_memory_human'] }}
                                            </span>
                                        @else
                                            {{-- Usar total_system_memory si maxmemory no está configurado --}}
                                            <span class="{{ ($redisStats['used_memory'] / $redisStats['total_system_memory']) > 0.8 ? 'text-danger' : 'text-success' }}">
                                                {{ $redisStats['used_memory_human'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tiempo de actividad</strong></td>
                                    <td>{{ $redisStats['uptime'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div>
            {{-- Botones --}}
            <div class="row my-4">
                <div class="col-lg-12 text-end">
                    <button
                        class="btn btn-secondary btn-clear-cache btn-sm mt-2 mr-2 waves-effect waves-light"
                        wire:click="reloadCacheStats"
                        data-loading-text="Actualizando...">
                        Actualizar
                    </button>
                </div>
            </div>
            {{-- Notifications --}}
            <div class="notification-container pt-4" wire:ignore></div>
        </div>
    </div>
</div>

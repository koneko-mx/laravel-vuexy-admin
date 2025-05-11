<div class="p-6 space-y-6">
    <div class="text-xl font-semibold text-gray-800">
        🧪 Diagnóstico del modelo <code>{{ get_class($model) }}</code>
    </div>

    @php
        $debug = method_exists($model, 'getExtendableDebugData') ? $model->getExtendableDebugData() : [];

        $attributes = $debug['attributes'] ?? [];
        $traits = $debug['traits'] ?? [];
        $methods = [];

        $methodTypes = ['fillable', 'cast', 'audit', 'hidden', 'appends'];

        foreach ($traits as $trait) {
            $traitName = class_basename($trait);
            foreach ($methodTypes as $type) {
                $method = 'get' . ucfirst($type) . $traitName;
                if (method_exists($model, $method)) {
                    try {
                        $methods[$traitName][$type] = $model->{$method}();
                    } catch (\Throwable $e) {
                        $methods[$traitName][$type] = ['⚠️ Error: ' . $e->getMessage()];
                    }
                }
            }
        }

        $simpleProps = [
            'hidden' => method_exists($model, 'getHidden') ? $model->getHidden() : ($model->hidden ?? []),
            'appends' => method_exists($model, 'getAppends') ? $model->getAppends() : ($model->appends ?? []),
        ];
    @endphp

    {{-- GRUPO: Atributos Extendidos --}}
    <div class="bg-white border rounded-xl shadow-sm">
        <div class="px-5 py-4 border-b bg-gradient-to-r from-gray-100 to-gray-50 rounded-t-xl">
            <h2 class="text-lg font-semibold text-gray-700">📦 Atributos extendidos del modelo</h2>
        </div>
        <div class="p-5 space-y-6">
            @foreach ($attributes as $type => $data)
                <div>
                    <h3 class="text-md font-semibold text-gray-800 uppercase tracking-wide mb-2">
                        {{ $type }}
                        <span class="text-xs text-gray-500">({{ $data['cache_key'] ?? 'sin cache' }})</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>
                            <p class="font-semibold text-gray-700 mb-1">Base</p>
                            <pre class="bg-gray-100 p-2 rounded text-xs">@json($data['base'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700 mb-1">Extendidos</p>
                            <pre class="bg-gray-100 p-2 rounded text-xs">@json($data['extended'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700 mb-1">Combinado</p>
                            <pre class="bg-gray-100 p-2 rounded text-xs">@json($data['combined'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-gray-500">
                        <span>🚀 Cache: <strong class="{{ $data['cache_enabled'] ? 'text-green-600' : 'text-red-600' }}">{{ $data['cache_enabled'] ? 'ACTIVO' : 'INACTIVO' }}</strong></span>
                        <span>🧪 Bypass: <strong class="{{ $data['bypass_cache'] ? 'text-yellow-600' : 'text-gray-600' }}">{{ $data['bypass_cache'] ? 'Sí (modo local)' : 'No' }}</strong></span>
                        <span>⏱ TTL: {{ $data['ttl'] }} seg</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- GRUPO: hidden y appends --}}
    <div class="bg-white border rounded-xl shadow-sm">
        <div class="px-5 py-4 border-b bg-gradient-to-r from-gray-100 to-gray-50 rounded-t-xl">
            <h2 class="text-lg font-semibold text-gray-700">🔐 Propiedades visibles y ocultas</h2>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
            @foreach($simpleProps as $key => $value)
                <div>
                    <h3 class="font-semibold mb-2 capitalize">{{ $key }}</h3>
                    <pre class="bg-gray-100 p-2 rounded text-xs">@json($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                </div>
            @endforeach
        </div>
    </div>

    {{-- GRUPO: Traits --}}
    <div class="bg-white border rounded-xl shadow-sm">
        <div class="px-5 py-4 border-b bg-gradient-to-r from-gray-100 to-gray-50 rounded-t-xl">
            <h2 class="text-lg font-semibold text-gray-700">🧠 Traits detectados</h2>
        </div>
        <div class="p-5">
            <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
                @foreach($traits as $trait)
                    <li><code class="text-indigo-600">{{ $trait }}</code></li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- GRUPO: Métodos por Trait --}}
    <div class="bg-white border rounded-xl shadow-sm">
        <div class="px-5 py-4 border-b bg-gradient-to-r from-gray-100 to-gray-50 rounded-t-xl">
            <h2 class="text-lg font-semibold text-gray-700">🔍 Métodos de extensión encontrados</h2>
        </div>
        <div class="p-5">
            <pre class="bg-gray-100 p-3 rounded text-xs text-gray-700">@json($methods, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
        </div>
    </div>
</div>

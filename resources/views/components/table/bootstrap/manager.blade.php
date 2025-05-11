@php
    use Illuminate\Support\Str;
@endphp

@props([
    'id'              => uniqid(),
    'tagName'         => '',
    'datatableConfig' => [],
    'noFilterButtons' => false
])

@php
    if($tagName){
        $id = 'bt-' . Str::kebab($tagName) . 's';
    }
@endphp

<div id="{{ $id }}" wire:ignore>
    {{-- Contenedor de notificaciones --}}
    <div class="notification-container"></div>

    {{-- Toolbar con filtros, agrupación y herramientas --}}
    <div class="bt-toolbar">
        <div class="d-flex flex-wrap">
            {{ $tools ?? '' }}

            @isset($filterButtons)
                {{ $filterButtons }}

            @elseif($noFilterButtons == false)
                <div class="my-1 pr-2">
                    <x-vuexy-admin::button.basic variant="secondary" class="bt-btn-refresh" icon="ti ti-zoom-reset" size="sm" label="Refrescar" />
                </div>
                <div class="my-1 pr-2">
                    <x-vuexy-admin::button.basic variant="secondary" class="bt-btn-filter-edit" label-style icon="ti ti-filter-edit" size="sm" label="Filtros" />
                </div>
                <div class="my-1 pr-2">
                    <x-vuexy-admin::button.basic variant="secondary" class="bt-btn-filter-cancel" text-style icon="ti ti-filter-cancel" size="sm" label="Limpiar filtros" />
                </div>
            @endisset

            {{ $postTools ?? '' }}

        </div>
    </div>

    {{-- Tabla con Bootstrap Table --}}
    <table class="bootstrap-table"></table>
</div>

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new BootstrapTableManager('#{{ $id }}', {!! json_encode($datatableConfig) !!});

            document.addEventListener("reload-{{ $id }}", () => {
                $("#{{ $id }} .bootstrap-table").bootstrapTable("refresh");
            });
        });
    </script>
    @isset($datatableConfig['routes'])
    <script id="app-routes" type="application/json">
        {!! json_encode($datatableConfig['routes']) !!}
    </script>
    @endisset
@endpush

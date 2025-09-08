@php
    use Koneko\VuexyAdmin\Models\User;

    $maxQuickLinks = config_m('core')->get('layout.vuexy.maxQuickLinks', 8);
@endphp

<li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
    <a class="nav-link btn btn-text-secondary btn-icon rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class='ti ti-layout-grid-add ti-md'></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0">
        <div class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">Atajos</h6>
                @if($isRouteAllowed)
                    @if($vuexyQuickLinks['current_page_in_list'])
                        <button
                            wire:click="remove('{{ Route::currentRouteName() }}')"
                            type="button"
                            class="btn btn-text-secondary rounded-pill btn-icon dropdown-shortcuts-remove"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Remover atajo">
                            <i class="ti ti-trash text-heading"></i>
                        </button>
                    @else
                        @if($vuexyQuickLinks['totalLinks'] < $maxQuickLinks)
                            <button
                                wire:click="add('{{ Route::currentRouteName() }}')"
                                type="button"
                                class="btn btn-text-secondary rounded-pill btn-icon dropdown-shortcuts-add"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar atajo">
                                <i class="ti ti-plus text-heading"></i>
                            </button>
                        @endif
                    @endif
                @endif
            </div>
        </div>
        <div class="dropdown-shortcuts-list scrollable-container">
            @foreach  ($vuexyQuickLinks['rows'] as $quickLinksRow)
                <div class="row row-bordered overflow-visible g-0">
                    @foreach  ($quickLinksRow as $key => $quickLink)
                        <div class="dropdown-shortcuts-item col @if ($quickLink['route'] === $currentRouteId) active @endif">
                            <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                                <i class="ti ti-{{ $quickLink['icon'] }} ti-26px text-heading"></i>
                            </span>
                            <a href="{{ $quickLink['url'] }}" class="stretched-link">{{ $quickLink['title'] }}</a>
                            <small>{{ $quickLink['subtitle'] }}</small>
                        </div>
                        @if ($key == 0 && !isset($quickLinksRow[1]))
                            <div class="dropdown-shortcuts-item col"></div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</li>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.addEventListener('refresh-tooltips', () => {
                initVuexyTooltips();

                setTimeout(() => {
                    window.Helpers.initNavbarDropdownScrollbar();
                }, 1);
            });

            function initVuexyTooltips() {
                // Limpia tooltips previos
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');

                tooltipTriggerList.forEach(el => {
                    const tooltip = bootstrap.Tooltip.getInstance(el);

                    if (tooltip) {
                        tooltip.dispose(); // elimina instancia previa
                    }
                });

                // Vuelve a inicializar
                tooltipTriggerList.forEach(el => {
                    new bootstrap.Tooltip(el);
                });
            }
        });
    </script>
@endpush

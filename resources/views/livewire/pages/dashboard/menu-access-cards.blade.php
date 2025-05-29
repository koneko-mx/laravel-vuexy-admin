@php
    /**
     * Vista Blade para mostrar los accesos rápidos.
     * Compatible con Vuexy Admin y modo oscuro.
     */
    $menuDebug = config_m()->get('menu.debug', []);

    $show_broken_routes   = $menuDebug['show_broken_routes'] ?? false;
    $show_disallowed_links = $menuDebug['show_disallowed_links'] ?? false;
    $show_hidden_items     = $menuDebug['show_hidden_items'] ?? false;
@endphp

<div class="space-y-8">
    <!-- 🔍 Campo de búsqueda -->
    <x-vuexy-admin::form.input
        id="quick_acces_search"
        placeholder="Buscar acceso rápido..."
        suffixIcon="ti ti-search"
        class="form-control-lg"
        mb0
        autocomplete="off"
        role="search"
        aria-label="Buscar acceso rápido"
    />
    @if($components)
        <div class="col-12 overflow-hidden mt-2">
            <div id="quick-access-scroll">
                <ul class="nav mb-1" id="quick-access-tabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-component-filter="all">Todos</button>
                    </li>
                    @foreach($components as $component)
                        <li class="nav-item">
                            <button class="nav-link" data-component-filter="{{ $component['tag'] }}">
                                {{ $component['label'] }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @foreach ($groups as $group)
        @if(!empty($group['cards']))
            <div class="quick-access-group">
                <!-- Título de categoría con icono -->
                <div class="d-flex align-items-center mb-3">
                    <i class="{{ $group['icon'] }} text-2xl text-primary"></i>
                    <h5 class="mb-0 ms-2 text-dark dark:text-white search-term">{{ $group['label'] }}</h5>
                </div>
                <!-- Descripción de categoría -->
                @if (!empty($group['description']))
                    <p class="text-muted">
                        {{ $group['description'] }}
                    </p>
                @endif
                <!-- Grid de accesos rápidos en formato de Cards -->
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
                    @foreach ($group['cards'] as $item)
                        <div class="col quick-access-card" data-component="{{ $item['component'] ?? 'unknown' }}">
                            <a href="{{ $item['url'] }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm hover:shadow-lg transition-all duration-300">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                        <i class="{{ $item['icon'] }} text-3xl text-primary mt-1 mb-3"></i>
                                        <h6 class="mb-0 text-dark dark:text-light fw-semibold search-term">
                                            {{ $item['title'] }}
                                            @if($show_broken_routes && $item['url'] == "javascript:;")
                                                <p class="text-xs m-0 pt-2 text-gray-500">
                                                    <span class="xs mr-1">❌</span> Sin URL válida
                                                </p>
                                            @endif
                                            @if($show_disallowed_links && $item['disallowed_link'])
                                                <p class="text-xs m-0 pt-2 text-gray-500">
                                                    <span class="text-sm mr-1">🔒</span> Sin permisos
                                                </p>
                                            @endif
                                            @if($show_hidden_items && $item['hidden_item'])
                                                <p class="text-xs m-0 pt-2 text-gray-500">
                                                    <span class="text-sm mr-1">🚧</span> Vista forzada
                                                </p>
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="pb-6"></div>
            </div>
        @endif
    @endforeach
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById('quick_acces_search');
            const categories  = document.querySelectorAll('.quick-access-group');
            const tabButtons  = document.querySelectorAll('#quick-access-tabs button');
            const normalize   = str => str?.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            const highlightMatch = (element, term) => {
                const text = element.dataset.originalText || element.textContent;
                element.dataset.originalText = text;
                element.innerHTML = term ? text.replace(new RegExp(`(${term})`, 'gi'), '<mark>$1</mark>') : text;
            };

            let currentComponentFilter = 'all';

            searchInput.focus();
            searchInput.addEventListener('input', applyFilters);

            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    currentComponentFilter = this.dataset.componentFilter;
                    tabButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    applyFilters();
                });
            });

            function applyFilters() {
                const searchTerm = normalize(searchInput.value.trim());
                const isSearchEmpty = searchTerm === '';

                categories.forEach(group => {
                    const groupTitleEl = group.querySelector('.search-term');
                    const groupTitle = normalize(groupTitleEl?.textContent);
                    const groupMatches = groupTitle.includes(searchTerm);

                    highlightMatch(groupTitleEl, searchTerm);

                    const cards = group.querySelectorAll('.quick-access-card');

                    let visibleCards = 0;

                    cards.forEach(card => {
                        const titleEl = card.querySelector('.search-term');
                        const titleText = normalize(titleEl?.textContent);
                        const cardComponent = card.dataset.component;

                        const matchesComponent = currentComponentFilter === 'all' || cardComponent === currentComponentFilter;
                        const matchesSearch = isSearchEmpty || titleText.includes(searchTerm) || groupMatches;

                        const match = matchesComponent && matchesSearch;

                        highlightMatch(titleEl, searchTerm);

                        card.classList.toggle('quick-hidden', !match);
                        card.style.display = match ? 'block' : 'none';
                        if (match) visibleCards++;
                    });

                    group.classList.toggle('quick-hidden', visibleCards === 0);
                });
            }
        });
    </script>
@endpush

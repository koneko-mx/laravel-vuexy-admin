@php
    /**
     * Vista Blade para mostrar los accesos rápidos.
     * Compatible con Vuexy Admin y modo oscuro.
     */
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
                            <button class="nav-link" data-component-filter="{{ $component['namespace'] }}">
                                {{ \Str::headline($component['label']) }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @foreach ($menuCards as $category)
        @if(!empty($category['cards']) || config('vuexy.debug.menu.show_all_hidden', false))
            <div class="quick-access-category">
                <!-- Título de categoría con icono -->
                <div class="d-flex align-items-center mb-3">
                    <i class="{{ $category['icon'] }} text-2xl text-primary"></i>
                    <h5 class="mb-0 ms-2 text-dark dark:text-white search-term">{{ $category['title'] }}</h5>
                </div>
                <!-- Descripción de categoría -->
                @if (!empty($category['description']))
                    <p class="text-muted">
                        {{ $category['description'] }}
                    </p>
                @endif
                <!-- Grid de accesos rápidos en formato de Cards -->
                @if (!empty($category['cards']))
                    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
                        @foreach ($category['cards'] as $item)
                            <div class="col quick-access-card" data-component="{{ $item['component'] ?? 'unknown' }}">
                                <a href="{{ $item['url'] }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm hover:shadow-lg transition-all duration-300">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                            <!-- Ícono -->
                                            <i class="{{ $item['icon'] }} text-3xl text-primary mt-1 mb-3"></i>

                                            <!-- Título -->
                                            <h6 class="mb-0 text-dark dark:text-light fw-semibold search-term">
                                                {{ $item['title'] }}
                                                @if(CoreModule::config('menu.debug.show_broken_routers') && $item['url'] == "javascript:;")
                                                    <p class="text-xs m-0 pt-2 text-gray-500">
                                                        <span class="xs mr-1">❌</span>
                                                        Sin URL valida
                                                    </p>
                                                @endif
                                                @if(CoreModule::config('menu.debug.show_disallowed_links') && $item['disallowed_link'])
                                                    <p class="text-xs m-0 pt-2 text-gray-500">
                                                        <span class="text-sm mr-1">🔒</span>
                                                        Sin permisos
                                                    </p>
                                                @endif
                                                @if(CoreModule::config('menu.debug.show_hidden_items') && $item['hidden_item'])
                                                    <p class="text-xs m-0 pt-2 text-gray-500">
                                                        <span class="text-sm mr-1">🚧</span>
                                                        Vista forzada
                                                    </p>
                                                @endif
                                            </h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted fst-italic">
                        No hay accesos rápidos en esta categoría.
                    </p>
                @endif
                <div class="pb-6"></div>
            </div>
        @endif
    @endforeach
</div>

@push('page-script')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const quickAccessScroll = document.getElementById('quick-access-scroll');
            const searchInput = document.getElementById('quick_acces_search');
            const categories  = document.querySelectorAll('.quick-access-category');
            const tabButtons  = document.querySelectorAll('#quick-access-tabs button');
            const normalize   = str => str?.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            const highlightMatch = (element, term) => {
                const text = element.dataset.originalText || element.textContent;

                element.dataset.originalText = text; // Save original

                if (!term) {
                    element.innerHTML = text;
                    return;
                }

                const regex = new RegExp(`(${term})`, 'gi');

                element.innerHTML = text.replace(regex, '<mark>$1</mark>');
            };

            let currentComponentFilter = 'all';


            // PerfectScroll
            if (quickAccessScroll) {
                new PerfectScrollbar(quickAccessScroll, {
                    suppressScrollY: true,
                    wheelPropagation: false
                });
            }


            // Buscador
            searchInput.focus();
            searchInput.addEventListener('input', applyFilters);


            // Tabs
            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    currentComponentFilter = this.dataset.componentFilter;

                    tabButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    applyFilters();
                });
            });


            // Método de filtrado
            function applyFilters() {
                const searchTerm = normalize(searchInput.value.trim());
                const isSearchEmpty = searchTerm === '';

                categories.forEach(category => {
                    let categoryMatch = false;
                    const categoryTitleEl = category.querySelector('.search-term');
                    const categoryTitle = normalize(categoryTitleEl?.textContent);
                    const categoryMatches = categoryTitle.includes(searchTerm);

                    highlightMatch(categoryTitleEl, searchTerm);

                    const cards = category.querySelectorAll('.quick-access-card');
                    let visibleCards = 0;

                    cards.forEach(card => {
                        const titleEl = card.querySelector('.search-term');
                        const titleText = normalize(titleEl?.textContent);
                        const cardComponent = card.dataset.component;

                        const matchesComponent = currentComponentFilter === 'all' || cardComponent === currentComponentFilter;
                        const matchesSearch = isSearchEmpty || titleText.includes(searchTerm) || categoryMatches;

                        const match = matchesComponent && matchesSearch;

                        highlightMatch(titleEl, searchTerm);

                        if (match) {
                            card.classList.remove('quick-hidden');
                            card.style.display = 'block';
                            requestAnimationFrame(() => {
                                card.classList.add('quick-showing');
                            });
                            visibleCards++;
                        } else {
                            card.classList.remove('quick-showing');
                            card.classList.add('quick-hidden');
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 400);
                        }
                    });

                    const showCategory = visibleCards > 0;

                    category.classList.toggle('quick-hidden', !showCategory);
                });
            }
        });
    </script>
@endpush

@php
/**
 * Vista Blade para mostrar los accesos rápidos.
 * Compatible con Vuexy Admin y modo oscuro.
 */
@endphp

<div class="p-6 space-y-8">
    @foreach ($quickAccessItems as $category)
        <div class="mb-8">
            <!-- Título de categoría con icono -->
            <div class="d-flex align-items-center mb-3">
                <i class="{{ $category['icon'] }} text-3xl text-primary"></i>
                <h5 class="mb-0 ms-2 text-dark dark:text-white">{{ $category['title'] }}</h5>
            </div>

            <!-- Descripción de categoría -->
            @if (!empty($category['description']))
                <p class="text-muted">
                    {{ $category['description'] }}
                </p>
            @endif

            <!-- Grid de accesos rápidos en formato de Cards -->
            @if (!empty($category['submenu']))
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
                    @foreach ($category['submenu'] as $item)
                        <div class="col">
                            <a href="{{ $item['url'] }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm hover:shadow-lg transition-all duration-300">
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">

                                        <!-- Ícono -->
                                        <i class="{{ $item['icon'] }} text-4xl text-primary mb-2"></i>

                                        <!-- Título -->
                                        <h6 class="mb-0 text-dark dark:text-light fw-semibold">
                                            {{ $item['title'] }}
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
        </div>
    @endforeach
</div>

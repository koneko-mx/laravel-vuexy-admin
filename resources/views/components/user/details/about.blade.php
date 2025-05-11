@props(['user'])

@php
    $locale = app()->getLocale();
    $roles = $user->roles ?? collect();
    $flags = $user->getRegisteredFlags() ?? [];
@endphp

<div class="card mb-4">
    <div class="card-body">

        {{-- SECCIÓN: DATOS PERSONALES --}}
        <small class="text-uppercase text-muted">Información</small>
        <ul class="list-unstyled my-3 py-1">
            <li class="d-flex align-items-center mb-3">
                <i class="ti ti-user ti-lg me-2 text-muted"></i>
                <span class="fw-medium me-1">Nombre:</span>
                <span>{{ $user->full_name ?? 'N/A' }}</span>
            </li>
            <li class="d-flex align-items-center mb-3">
                <i class="ti ti-check ti-lg me-2 text-muted"></i>
                <span class="fw-medium me-1">Estado:</span>
                <span>{{ $user->status_label ?? 'Sin estado' }}</span>
            </li>
            <li class="d-flex align-items-start mb-3">
                <i class="ti ti-shield ti-lg me-2 text-muted"></i>
                <div>
                    <small class="card-text text-uppercase text-muted small">Roles</small>
                    <ul class="list-unstyled my-3 py-1 d-flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                            @php
                                $meta = json_decode($role->ui_metadata, true) ?? [];
                                $icon = $meta['icon'] ?? 'ti ti-user';
                                $style = $meta['style'] ?? 'primary'; // default badge style
                                $description = $meta['description'][app()->getLocale()] ?? $role->name;
                            @endphp
                            <li>
                                <span class="badge bg-label-{{ $style }} d-inline-flex align-items-center gap-1"
                                      data-bs-toggle="tooltip"
                                      data-bs-placement="top"
                                      title="{{ $description }}">
                                    <i class="{{ $icon }} mr-1"></i> {{ $role->name }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
            @if(!empty($flags))
                <li class="d-flex align-items-start mb-3">
                    <i class="ti ti-flag ti-lg me-2 text-muted"></i>
                    <div>
                        <div class="fw-medium">Flags:</div>
                        <span class="text-muted">{{ implode(', ', array_keys($flags)) }}</span>
                    </div>
                </li>
            @endif
        </ul>

        {{-- SECCIÓN: CONTACTO --}}
        <small class="text-uppercase text-muted">Contacto</small>
        <ul class="list-unstyled my-3 py-1">
            <li class="d-flex align-items-center mb-3">
                <i class="ti ti-mail ti-lg me-2 text-muted"></i>
                <span class="fw-medium me-1">Email:</span>
                <span>{{ $user->email }}</span>
            </li>
            <li class="d-flex align-items-center mb-3">
                <i class="ti ti-phone ti-lg me-2 text-muted"></i>
                <span class="fw-medium me-1">Teléfono:</span>
                <span>{{ $user->phone ?? 'No registrado' }}</span>
            </li>
            <li class="d-flex align-items-center mb-3">
                <i class="ti ti-brand-skype ti-lg me-2 text-muted"></i>
                <span class="fw-medium me-1">Skype:</span>
                <span>{{ $user->skype ?? 'No registrado' }}</span>
            </li>
        </ul>

        {{-- SECCIÓN: OTROS --}}
        <small class="text-uppercase text-muted">Otros</small>
        <ul class="list-unstyled mt-3 pt-1">
            <li class="d-flex align-items-center mb-3">
                <i class="ti ti-world ti-lg me-2 text-muted"></i>
                <span class="fw-medium me-1">País:</span>
                <span>{{ $user->country ?? 'No disponible' }}</span>
            </li>
            <li class="d-flex align-items-center mb-3">
                <i class="ti ti-language ti-lg me-2 text-muted"></i>
                <span class="fw-medium me-1">Idioma(s):</span>
                <span>{{ $user->languages ?? 'Español' }}</span>
            </li>
        </ul>

        {{-- SECCIÓN: EQUIPOS --}}
        @if(!empty($user->teams))
            <small class="text-uppercase text-muted">Equipos</small>
            <ul class="list-unstyled mt-3 pt-1">
                @forelse($user->teams as $team)
                    <li class="d-flex align-items-center mb-2">
                        <i class="ti ti-users me-2 text-muted"></i>
                        <span class="fw-medium me-1">{{ $team['name'] }}</span>
                        <span class="text-muted">({{ $team['members'] }} miembros)</span>
                    </li>
                @empty
                    <li class="text-muted">Sin equipos asignados</li>
                @endforelse
            </ul>
        @endif
    </div>
</div>

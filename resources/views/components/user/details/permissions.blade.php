@props(['user' => null])

@php
    use Illuminate\Support\Str;
    use Koneko\VuexyAdmin\Models\PermissionGroup;

    $permissions = $user?->getAllPermissionMetas() ?? collect();
    $groupedPermissions = $permissions->groupBy(fn($perm) => optional($perm->group)->module ?? 'Sin módulo');

    $moduleDescriptions = PermissionGroup::query()
        ->where('type', 'module')
        ->get()
        ->keyBy('module')
        ->map(fn($group) => [
            'name' => $group->name[app()->getLocale()] ?? $group->module,
            'description' => $group->ui_metadata['description'][app()->getLocale()] ?? '',
            'icon' => $group->ui_metadata['icon'] ?? 'ti ti-box',
        ]);
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        @forelse($groupedPermissions as $module => $groupItems)
            @php
                $subGroups = $groupItems->groupBy(fn($perm) => optional($perm->group)->sub_grupo ?? 'General');
                $moduleInfo = $moduleDescriptions[$module] ?? null;
            @endphp

            <div class="col-md-12 col-xl-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-start pb-1">
                        {{-- Sección izquierda: ícono + título --}}
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="{{ $moduleInfo['icon'] ?? 'ti ti-box' }} text-lg text-muted"></i>
                                <div>
                                    <div class="fw-semibold">{{ $moduleInfo['name'] ?? $module }}</div>
                                    <small class="text-muted small">{{ $module }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- Contador de permisos --}}
                        <div class="text-end">
                            <span class="text-primary fw-semibold small" aria-label="Total de permisos">
                                {{ $groupItems->count() }} permiso{{ $groupItems->count() === 1 ? '' : 's' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body py-0">
                        {{-- Descripción general del módulo --}}
                        @if(!empty($moduleInfo['description']))
                            <p class="mb-0">{{ $moduleInfo['description'] }}</p>
                        @endif
                    </div>
                    <div class="card-body pt-0">
                        @foreach($subGroups as $sub => $items)
                            <hr>
                            <div class="mt-3">
                                <h6 class="text-primary mb-1">
                                    {{ Str::headline($sub) }}
                                    <span class="text-muted fw-normal">({{ $items->count() }})</span>
                                </h6>

                                @php
                                    $groupInfo = $items->first()?->group;
                                    $subGroupDescription = $groupInfo->ui_metadata['description'][app()->getLocale()] ?? null;

                                    $actions = $items
                                        ->filter(fn($perm) => method_exists($perm, 'getActionLabel'))
                                        ->map(fn($perm) => $perm->getActionLabel())
                                        ->unique()
                                        ->implode(', ');
                                @endphp

                                @if($subGroupDescription)
                                    <p class="small mb-3">{{ $subGroupDescription }}</p>
                                @endif

                                @if($actions)
                                    <p class="text-muted small mb-2">Acciones: {{ $actions }}</p>
                                @endif

                                <ul class="list-unstyled small">
                                    @foreach($items as $perm)
                                        <li class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center px-2 py-1">
                                                <div class="d-flex align-items-center gap-1 flex-grow-1 text-wrap">
                                                    <i class="ti ti-key text-muted"></i>
                                                    <span class="text-break">
                                                        {{ $perm->getDisplayName() }}
                                                    </span>
                                                </div>
                                                <i class="ti ti-code text-muted cursor-pointer"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   data-bs-original-title="{{ $perm->name }}">
                                                </i>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>



                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">Este usuario no tiene permisos asignados.</div>
            </div>
        @endforelse
    </div>
</div>

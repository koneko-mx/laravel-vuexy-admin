<div>
    <h2 class="text-xl font-bold">Gestión de Roles</h2>

    <!-- Crear Nuevo Rol -->
    <div class="mb-4">
        <input type="text" wire:model="roleName" placeholder="Nombre del rol" class="border p-2">
        <button wire:click="createRole" class="bg-blue-500 text-white px-4 py-2">Crear Rol</button>
    </div>

    <!-- Tabla de Roles -->
    <table class="table-auto w-full border">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>
                        <button wire:click="selectRole({{ $role->id }})" class="text-blue-500">Editar</button>
                        <button wire:click="deleteRole({{ $role->id }})" class="text-red-500">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $roles->links() }}

    <!-- Editar Permisos del Rol -->
    @if($selectedRole)
    <div class="mt-4">
        <h3>Permisos para {{ $selectedRole->name }}</h3>
        @foreach($availablePermissions as $permission)
            <label>
                <input type="checkbox" wire:model="permissions" value="{{ $permission->id }}">
                {{ $permission->name }}
            </label>
        @endforeach
        <button wire:click="updateRolePermissions" class="bg-green-500 text-white px-4 py-2 mt-2">Actualizar</button>
    </div>
    @endif
</div>

<div>
    <h2 class="text-xl font-bold">Gestión de Permisos</h2>

    <div class="mb-4">
        <input type="text" wire:model="permissionName" placeholder="Nombre del permiso" class="border p-2">
        <button wire:click="createPermission" class="bg-blue-500 text-white px-4 py-2">Crear Permiso</button>
    </div>

    <ul>
        @foreach($permissions as $permission)
            <li>
                {{ $permission->name }}
                <button wire:click="deletePermission({{ $permission->id }})" class="text-red-500">Eliminar</button>
            </li>
        @endforeach
    </ul>
</div>

<x-vuexy-admin::table.bootstrap.manager :tagName="$tagName" :datatableConfig="$bt_datatable">
    <x-slot name="tools">
        <div class="mb-4 pr-2">
            <x-vuexy-admin::button.index-offcanvas :label="$singularName" :tagName="$tagName" />
        </div>
    </x-slot>
</x-vuexy-admin::table.bootstrap.manager>

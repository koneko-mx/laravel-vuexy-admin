<?php

namespace Koneko\VuexyAdmin\Livewire\Users;

use Illuminate\Validation\Rule;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Livewire\Form\AbstractFormComponent;
use Koneko\SatCatalogs\Models\{Colonia, Estado, Localidad, Municipio, Pais, RegimenFiscal};
use Koneko\VuexyAdmin\Models\Store;

/**
 * Class UserForm
 *
 * Componente Livewire para manejar el formulario CRUD de sucursales en el sistema ERP.
 * Implementa la creación, edición y eliminación de sucursales con validaciones dinámicas.
 */
class UserForm extends AbstractFormComponent
{
    /**
     * Campos específicos del formulario.
     */
    public $code, $name, $description, $manager_id, $rfc, $nombre_fiscal, $c_regimen_fiscal,
        $domicilio_fiscal, $serie_ingresos, $serie_egresos, $serie_pagos, $c_codigo_postal,
        $c_pais, $c_estado, $c_localidad, $c_municipio, $c_colonia, $direccion, $num_ext,
        $num_int, $email, $tel, $tel2, $lat, $lng, $show_on_website, $enable_ecommerce, $status;


        public $confirmDeletion = false;

    /**
     * Listas de opciones para selects en el formulario.
     */
    public $manager_id_options = [],
        $c_regimen_fiscal_options = [],
        $c_pais_options = [],
        $c_estado_options = [],
        $c_localidad_options = [],
        $c_municipio_options = [],
        $c_colonia_options = [];

    /**
     * Montar el formulario e inicializar datos específicos.
     *
     * @param string $mode Modo del formulario: create, edit, delete.
     * @param Store|null $store El modelo Store si está en modo edición o eliminación.
     */
    public function mount(string $mode = 'create', mixed $store = null): void
    {
        parent::mount($mode, $store->id ?? null);
    }

    /**
     * Cargar opciones de formularios según el modo actual.
     *
     * @param string $mode
     */
    private function loadOptions(string $mode): void
    {
        $this->manager_id_options = User::getUsersListWithInactive($this->manager_id, ['type' => 'user', 'status' => 1]);
        $this->c_regimen_fiscal_options = RegimenFiscal::selectList();
        $this->c_pais_options = Pais::selectList();
        $this->c_estado_options = Estado::selectList($this->c_pais)->toArray();

        if ($mode !== 'create') {
            $this->c_localidad_options = Localidad::selectList($this->c_estado)->toArray();
            $this->c_municipio_options = Municipio::selectList($this->c_estado, $this->c_municipio)->toArray();
            $this->c_colonia_options = Colonia::selectList($this->c_codigo_postal, $this->c_colonia)->toArray();
        }
    }

    // ===================== MÉTODOS OBLIGATORIOS =====================

    /**
     * Devuelve el modelo Eloquent asociado.
     *
     * @return string
     */
    protected function model(): string
    {
        return Store::class;
    }

    /**
     * Reglas de validación dinámicas según el modo actual.
     *
     * @param string $mode
     * @return array
     */
    protected function dynamicRules(string $mode): array
    {
        switch ($mode) {
            case 'create':
            case 'edit':
                return [
                    'code'              => [
                        'required', 'string', 'alpha_num', 'max:16',
                        Rule::unique('stores', 'code')->ignore($this->id)
                    ],
                    'name'              => 'required|string|max:96',
                    'description'       => 'nullable|string|max:1024',
                    'manager_id'        => 'nullable|exists:users,id',

                    // Información fiscal
                    'rfc'               => ['nullable', 'string', 'regex:/^([A-ZÑ&]{3,4})(\d{6})([A-Z\d]{3})$/i', 'max:13'],
                    'nombre_fiscal'     => 'nullable|string|max:255',
                    'c_regimen_fiscal'  => 'nullable|exists:sat_regimen_fiscal,c_regimen_fiscal',
                    'domicilio_fiscal'  => 'nullable|exists:sat_codigo_postal,c_codigo_postal',

                    // Ubicación
                    'c_pais'            => 'nullable|exists:sat_pais,c_pais|string|size:3',
                    'c_estado'          => 'nullable|exists:sat_estado,c_estado|string|min:2|max:3',
                    'c_municipio'       => 'nullable|exists:sat_municipio,c_municipio|integer',
                    'c_localidad'       => 'nullable|integer',
                    'c_codigo_postal'   => 'nullable|exists:sat_codigo_postal,c_codigo_postal|integer',
                    'c_colonia'         => 'nullable|exists:sat_colonia,c_colonia|integer',
                    'direccion'         => 'nullable|string|max:255',
                    'num_ext'           => 'nullable|string|max:50',
                    'num_int'           => 'nullable|string|max:50',
                    'lat'               => 'nullable|numeric|between:-90,90',
                    'lng'               => 'nullable|numeric|between:-180,180',

                    // Contacto
                    'email'             => ['nullable', 'email', 'required_if:enable_ecommerce,true'],
                    'tel'               => ['nullable', 'regex:/^[0-9\s\-\+\(\)]+$/', 'max:15'],
                    'tel2'              => ['nullable', 'regex:/^[0-9\s\-\+\(\)]+$/', 'max:15'],

                    // Configuración web y estado
                    'show_on_website'   => 'nullable|boolean',
                    'enable_ecommerce'  => 'nullable|boolean',
                    'status'            => 'nullable|boolean',
                ];

            case 'delete':
                return [
                    'confirmDeletion' => 'accepted', // Asegura que el usuario confirme la eliminación
                ];

            default:
                return [];
        }
    }

    /**
     * Inicializa los datos del formulario en función del modo.
     *
     * @param Store|null $store
     * @param string $mode
     */
    protected function initializeFormData(mixed $store, string $mode): void
    {
         if ($store) {
             $this->code = $store->code;
             $this->name = $store->name;
             $this->description = $store->description;
             $this->manager_id = $store->manager_id;
             $this->rfc = $store->rfc;
             $this->nombre_fiscal = $store->nombre_fiscal;
             $this->c_regimen_fiscal = $store->c_regimen_fiscal;
             $this->domicilio_fiscal = $store->domicilio_fiscal;
             $this->c_pais = $store->c_pais;
             $this->c_estado = $store->c_estado;
             $this->c_municipio = $store->c_municipio;
             $this->c_localidad = $store->c_localidad;
             $this->c_codigo_postal = $store->c_codigo_postal;
             $this->c_colonia = $store->c_colonia;
             $this->direccion = $store->direccion;
             $this->num_ext = $store->num_ext;
             $this->num_int = $store->num_int;
             $this->lat = $store->lat;
             $this->lng = $store->lng;
             $this->email = $store->email;
             $this->tel = $store->tel;
             $this->tel2 = $store->tel2;
             $this->show_on_website = (bool) $store->show_on_website;
             $this->enable_ecommerce = (bool) $store->enable_ecommerce;
             $this->status = (bool) $store->status;

         } else {
             $this->c_pais = 'MEX';
             $this->status = true;
             $this->show_on_website = false;
             $this->enable_ecommerce = false;
         }

         $this->loadOptions($mode);
    }

    /**
     * Prepara los datos validados para su almacenamiento.
     *
     * @param array $validatedData
     * @return array
     */
    protected function prepareData(array $validatedData): array
    {
        return [
            'code'             => $validatedData['code'],
            'name'             => $validatedData['name'],
            'description'      => strip_tags($validatedData['description']),
            'manager_id'       => $validatedData['manager_id'],
            'rfc'              => $validatedData['rfc'],
            'nombre_fiscal'    => $validatedData['nombre_fiscal'],
            'c_regimen_fiscal' => $validatedData['c_regimen_fiscal'],
            'domicilio_fiscal' => $validatedData['domicilio_fiscal'],
            'c_codigo_postal'  => $validatedData['c_codigo_postal'],
            'c_pais'           => $validatedData['c_pais'],
            'c_estado'         => $validatedData['c_estado'],
            'c_localidad'      => $validatedData['c_localidad'],
            'c_municipio'      => $validatedData['c_municipio'],
            'c_colonia'        => $validatedData['c_colonia'],
            'direccion'        => $validatedData['direccion'],
            'num_ext'          => $validatedData['num_ext'],
            'num_int'          => $validatedData['num_int'],
            'email'            => $validatedData['email'],
            'tel'              => $validatedData['tel'],
            'tel2'             => $validatedData['tel2'],
            'lat'              => $validatedData['lat'],
            'lng'              => $validatedData['lng'],
            'status'           => $validatedData['status'],
            'show_on_website'  => $validatedData['show_on_website'],
            'enable_ecommerce' => $validatedData['enable_ecommerce'],
        ];
    }

    /**
     * Definición de los contenedores de notificación.
     *
     * @return array
     */
    protected function targetNotifies(): array
    {
        return [
            "index" => "#bt-stores .notification-container",
            "form" => "#store-form .notification-container",
        ];
    }

    /**
     * Ruta de vista asociada al formulario.
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function viewPath(): string
    {
        return 'vuexy-store-manager::livewire.stores.form';
    }

    // ===================== VALIDACIONES =====================

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => 'código de sucursal',
            'name' => 'nombre de la sucursal',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'El código de la sucursal es obligatorio.',
            'code.unique' => 'Este código ya está en uso por otra sucursal.',
            'name.required' => 'El nombre de la sucursal es obligatorio.',
        ];
    }

    // ===================== PREPARACIÓN DE DATOS =====================

    // ===================== NOTIFICACIONES Y EVENTOS =====================

    /**
     * Definición de los eventos del componente.
     *
     * @return array
     */
    protected function dispatches(): array
    {
        return [
            'on-failed-validation' => 'on-failed-validation-store',
            'on-hydrate' => 'on-hydrate-store-modal',
        ];
    }

    // ===================== REDIRECCIÓN =====================

    /**
     * Define la ruta de redirección tras guardar o eliminar.
     *
     * @return string
     */
    protected function getRedirectRoute(): string
    {
        return 'admin.core.user.index';
    }

}

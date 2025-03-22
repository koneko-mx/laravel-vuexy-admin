<?php

namespace Koneko\VuexyAdmin\Livewire\Users;

use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Livewire\Table\AbstractIndexComponent;

class UsersIndex extends AbstractIndexComponent
{
    /**
     * Almacena rutas útiles para la funcionalidad de edición o eliminación.
     */
    public $routes = [];

    /**
     * Método que define la clase o instancia del modelo a usar en este Index.
     */
    protected function model(): string
    {
        return User::class;
    }

    /**
     * Retorna las columnas (header) de la tabla.
     */
    protected function columns(): array
    {
        return [
            'action'            => 'Acciones',
            'full_name'         => 'Nombre completo',
            'email'             => 'Correo electrónico',
            'email_verified_at' => 'Correo verificado',
            'created_by'        => 'Creado Por',
            'status'            => 'Estatus',
            'created_at'        => 'Fecha Creación',
            'updated_at'        => 'Última Modificación',
        ];
    }

    /**
     * Retorna el formato (formatter) para cada columna.
     */
    protected function format(): array
    {
        return [
            'action' => [
                'formatter'     => 'userActionFormatter',
                'onlyFormatter' => true,
            ],
            'code' => [
                'formatter' => [
                    'name'   => 'dynamicBadgeFormatter',
                    'params' => ['color' => 'secondary'],
                ],
                'align'      => 'center',
                'switchable' => false,
            ],
            'full_name' => [
                'formatter' => 'userProfileFormatter',
            ],
            'email' => [
                'formatter' => 'emailFormatter',
                'visible'   => false,
            ],
            'email_verified_at' => [
                'visible'   => false,
            ],
            'parent_id' => [
                'formatter' => 'parentProfileFormatter',
                'visible'   => false,
            ],
            'agent_id' => [
                'formatter' => 'agentProfileFormatter',
                'visible'   => false,
            ],
            'phone' => [
                'formatter' => 'telFormatter',
                'visible'   => false,
            ],
            'mobile' => [
                'formatter' => 'telFormatter',
            ],
            'whatsapp' => [
                'formatter' => 'whatsappFormatter',
            ],
            'company' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'curp' => [
                'visible'   => false,
            ],
            'nss' => [
                'visible'   => false,
            ],
            'license_number' => [
                'visible'   => false,
            ],
            'job_position' => [
                'formatter' => 'textNowrapFormatter',
                'visible'   => false,
            ],
            'pais' => [
                'visible'   => false,
            ],
            'rfc' => [
                'visible'   => false,
            ],
            'nombre_fiscal' => [
                'formatter' => 'textNowrapFormatter',
                'visible'   => false,
            ],

            'domicilio_fiscal' => [
                'visible'   => false,
            ],
            'c_uso_cfdi' => [
                'formatter' => 'usoCfdiFormatter',
                'visible'   => false,
            ],
            'tipo_persona' => [
                'formatter' => 'dynamicBadgeFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
            'c_regimen_fiscal' => [
                'formatter' => 'regimenFiscalFormatter',
                'visible'   => false,
            ],
            'birth_date' => [
                'align'      => 'center',
                'visible'   => false,
            ],
            'hire_date' => [
                'align'      => 'center',
                'visible'   => false,
            ],
            'estado' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'municipio' => [
                'formatter' => 'textNowrapFormatter',
            ],
            'localidad' => [
                'formatter' => 'textNowrapFormatter',
                'visible'   => false,
            ],
            'is_partner' => [
                'formatter' => [
                    'name'   => 'dynamicBooleanFormatter',
                    'params' => ['tag' => 'checkSI'],
                ],
                'align'     => 'center',
            ],
            'is_employee' => [
                'formatter' => [
                    'name'   => 'dynamicBooleanFormatter',
                    'params' => ['tag' => 'checkSI'],
                ],
                'align'     => 'center',
            ],
            'is_prospect' => [
                'formatter' => [
                    'name'   => 'dynamicBooleanFormatter',
                    'params' => ['tag' => 'checkSI'],
                ],
                'align'     => 'center',
            ],
            'is_customer' => [
                'formatter' => [
                    'name'   => 'dynamicBooleanFormatter',
                    'params' => ['tag' => 'checkSI'],
                ],
                'align'     => 'center',
            ],
            'is_supplier' => [
                'formatter' => [
                    'name'   => 'dynamicBooleanFormatter',
                    'params' => ['tag' => 'checkSI'],
                ],
                'align'     => 'center',
            ],
            'is_carrier' => [
                'formatter' => [
                    'name'   => 'dynamicBooleanFormatter',
                    'params' => ['tag' => 'checkSI'],
                ],
                'align'     => 'center',
            ],
            'status' => [
                'formatter' => 'statusIntBadgeBgFormatter',
                'align' => 'center',
            ],
            'creator' => [
                'formatter' => 'creatorFormatter',
                'visible'   => false,
            ],
            'created_at' => [
                'formatter' => 'textNowrapFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
            'updated_at' => [
                'formatter' => 'textNowrapFormatter',
                'align'     => 'center',
                'visible'   => false,
            ],
        ];
    }

    /**
     * Montamos el componente y llamamos al parent::mount() para configurar la tabla.
     */
    public function mount(): void
    {
        parent::mount();

        // Definimos las rutas específicas de este componente
        $this->routes = [
            'admin.user.show'   => route('admin.core.users.show',   ['user' => ':id']),
            'admin.user.edit'   => route('admin.core.users.edit',   ['user' => ':id']),
            'admin.user.delete' => route('admin.core.users.delete', ['user' => ':id']),
        ];
    }

    /**
     * Retorna la vista a renderizar por este componente.
     */
    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.users.index';
    }
}

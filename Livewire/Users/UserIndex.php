<?php

namespace Koneko\VuexyAdmin\Livewire\Users;

use Livewire\WithFileUploads;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Livewire\Table\AbstractIndexComponent;

class UserIndex extends AbstractIndexComponent
{
    use WithFileUploads;

    public $doc_file;
    public $dropzoneVisible = true;

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
            'action'          => 'Acciones',
            'code'            => 'Código personal',
            'full_name'       => 'Nombre Completo',
            'email'           => 'Correo Electrónico',
            'parent_name'     => 'Responsable',
            'parent_email'    => 'Correo Responsable',
            'company'         => 'Empresa',
            'birth_date'      => 'Fecha de Nacimiento',
            'hire_date'       => 'Fecha de Contratación',
            'curp'            => 'CURP',
            'nss'             => 'NSS',
            'job_title'       => 'Puesto',
            'rfc'             => 'RFC',
            'nombre_fiscal'   => 'Nombre Fiscal',
            'profile_photo_path' => 'Foto de Perfil',
            'is_partner'      => 'Socio',
            'is_employee'     => 'Empleado',
            'is_prospect'     => 'Prospecto',
            'is_customer'     => 'Cliente',
            'is_provider'     => 'Proveedor',
            'is_user'         => 'Usuario',
            'status'          => 'Estatus',
            'creator'         => 'Creado Por',
            'creator_email'   => 'Correo Creador',
            'created_at'      => 'Fecha de Creación',
            'updated_at'      => 'Última Modificación',
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
            'parent_name' => [
                'formatter' => 'contactParentFormatter',
                'visible'   => false,
            ],
            'agent_name' => [
                'formatter' => 'agentFormatter',
                'visible'   => false,
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
            'job_title' => [
                'formatter' => 'textNowrapFormatter',
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
            'is_provider' => [
                'formatter' => [
                    'name'   => 'dynamicBooleanFormatter',
                    'params' => ['tag' => 'checkSI'],
                ],
                'align'     => 'center',
            ],
            'is_user' => [
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
     * Procesa el documento recibido (CFDI XML o Constancia PDF).
     */
    public function processDocument()
    {
        // Verificamos si el archivo es válido
        if (!$this->doc_file instanceof UploadedFile) {
            return $this->addError('doc_file', 'No se pudo recibir el archivo.');
        }

        try {
            // Validar tipo de archivo
            $this->validate([
                'doc_file' => 'required|mimes:pdf,xml|max:2048'
            ]);


        // **Detectar el tipo de documento**
        $extension = strtolower($this->doc_file->getClientOriginalExtension());

        // **Procesar según el tipo de archivo**
        switch ($extension) {
            case 'xml':
                $service = new FacturaXmlService();
                $data = $service->processUploadedFile($this->doc_file);
                break;

            case 'pdf':
                $service = new ConstanciaFiscalService();
                $data = $service->extractData($this->doc_file);
                break;

            default:
                throw new Exception("Formato de archivo no soportado.");
        }

        dd($data);

        // **Asignar los valores extraídos al formulario**
        $this->rfc      = $data['rfc'] ?? null;
        $this->name     = $data['name'] ?? null;
        $this->email    = $data['email'] ?? null;
        $this->tel      = $data['telefono'] ?? null;
        //$this->direccion = $data['domicilio_fiscal'] ?? null;

        // Ocultar el Dropzone después de procesar
        $this->dropzoneVisible = false;

        } catch (ValidationException $e) {
            $this->handleValidationException($e);

        } catch (QueryException $e) {
            $this->handleDatabaseException($e);

        } catch (ModelNotFoundException $e) {
            $this->handleException('danger', 'Registro no encontrado.');

        } catch (Exception $e) {
            $this->handleException('danger', 'Error al procesar el archivo: ' . $e->getMessage());
        }
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

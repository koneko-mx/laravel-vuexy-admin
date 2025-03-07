<?php

namespace Koneko\VuexyAdmin\Livewire\Users;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Koneko\VuexyAdmin\Livewire\Form\AbstractFormOffCanvasComponent;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyContacts\Services\{ContactCatalogService,ConstanciaFiscalService,FacturaXmlService};
use Koneko\VuexyStoreManager\Services\StoreCatalogService;
use Livewire\WithFileUploads;

/**
 * Class UserOffCanvasForm
 *
 * Componente Livewire para gestionar almacenes.
 * Extiende la clase AbstractFormOffCanvasComponent e implementa validaciones dinámicas,
 * manejo de formularios, eventos y actualizaciones en tiempo real.
 *
 * @package Koneko\VuexyAdmin\Livewire\Users
 */
class UserOffCanvasForm extends AbstractFormOffCanvasComponent
{
    use WithFileUploads;

    public $doc_file;
    public $dropzoneVisible = true;


    /**
     * Propiedades del formulario relacionadas con el usuario.
     */
    public $code,
        $parent_id,
        $name,
        $last_name,
        $email,
        $company,
        $rfc,
        $nombre_fiscal,
        $tipo_persona,
        $c_regimen_fiscal,
        $domicilio_fiscal,
        $is_partner,
        $is_employee,
        $is_prospect,
        $is_customer,
        $is_provider,
        $status;

    /**
     * Listas de opciones para selects en el formulario.
     */
    public $store_options = [],
        $work_center_options = [],
        $manager_options = [];

    /**
     * Eventos de escucha de Livewire.
     *
     * @var array
     */
    protected $listeners = [
        'editUsers' => 'loadFormModel',
        'confirmDeletionUsers' => 'loadFormModelForDeletion',
    ];

    /**
     * Definición de tipos de datos que se deben castear.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Define el modelo Eloquent asociado con el formulario.
     *
     * @return string
     */
    protected function model(): string
    {
        return User::class;
    }

    /**
     * Define los campos del formulario.
     *
     * @return array<string, mixed>
     */
    protected function fields(): array
    {
        return (new User())->getFillable();
    }

    /**
     * Valores por defecto para el formulario.
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            //
        ];
    }

    /**
     * Campo que se debe enfocar cuando se abra el formulario.
     *
     * @return string
     */
    protected function focusOnOpen(): string
    {
        return 'name';
    }

    /**
     * Define reglas de validación dinámicas basadas en el modo actual.
     *
     * @param string $mode El modo actual del formulario ('create', 'edit', 'delete').
     * @return array
     */
    protected function dynamicRules(string $mode): array
    {
        switch ($mode) {
            case 'create':
            case 'edit':
                return [
                    'code'  => ['required', 'string', 'max:16', Rule::unique('contact', 'code')->ignore($this->id)],
                    'name'  => ['required', 'string', 'max:96'],
                    'notes' => ['nullable', 'string', 'max:1024'],
                    'tel'   => ['nullable', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
                ];

            case 'delete':
                return [
                    'confirmDeletion' => 'accepted', // Asegura que el usuario confirme la eliminación
                ];

            default:
                return [];
        }
    }

    // ===================== VALIDACIONES =====================

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    protected function attributes(): array
    {
        return [
            'code' => 'código de usuario',
            'name' => 'nombre del usuario',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'code.unique' => 'Este código ya está en uso por otro usuario.',
            'name.required' => 'El nombre del usuario es obligatorio.',
        ];
    }

    /**
     * Carga el formulario con datos del usuario y actualiza las opciones dinámicas.
     *
     * @param int $id
     */
    public function loadFormModel($id): void
    {
        parent::loadFormModel($id);

        $this->work_center_options = $this->store_id
            ? DB::table('store_work_centers')
                ->where('store_id', $this->store_id)
                ->pluck('name', 'id')
                ->toArray()
            : [];
    }

    /**
     * Carga el formulario para eliminar un usuario, actualizando las opciones necesarias.
     *
     * @param int $id
     */
    public function loadFormModelForDeletion($id): void
    {
        parent::loadFormModelForDeletion($id);

        $this->work_center_options = DB::table('store_work_centers')
            ->where('store_id', $this->store_id)
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Define las opciones de los selectores desplegables.
     *
     * @return array
     */
    protected function options(): array
    {
        $storeCatalogService = app(StoreCatalogService::class);
        $contactCatalogService = app(ContactCatalogService::class);

        return [
            'store_options' => $storeCatalogService->searchCatalog('stores', '', ['limit' => -1]),
            'manager_options' => $contactCatalogService->searchCatalog('users', '', ['limit' => -1]),
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
     * Ruta de la vista asociada con este formulario.
     *
     * @return string
     */
    protected function viewPath(): string
    {
        return 'vuexy-admin::livewire.users.offcanvas-form';
    }
}

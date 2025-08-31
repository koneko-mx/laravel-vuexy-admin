<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Livewire\Components\Form;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Clase base abstracta para manejar formularios Off-Canvas con Livewire.
 *
 * Ofrece:
 * - CRUD con transacciones
 * - Validación según modo (create, edit, delete)
 * - Metadata del modelo
 * - Notificaciones y manejo de errores
 * - Casteo automático de tipos
 */
abstract class AbstractFormOffCanvasComponent extends Component
{
    // ===================== CONFIGURACIÓN DEL FORMULARIO =====================

    public string $uniqueId;
    public string $mode;
    public int|string|null $id = null;

    // ===================== METADATOS DEL MODELO =====================

    /** @var string ID del offcanvas */
    public string $offcanvasId;

    /** @var string ID del formulario */
    public ?string $formId = null;

    /** @var string Etiqueta del modelo */
    public string $tagName;

    /** @var string Nombre singular del modelo */
    public string $singularName;

    /** @var string Nombre legible del modelo */
    public string $displayName;

    /** @var string|false|null Columna que recibe foco al abrir el formulario */
    public string|false|null $focusColumnOnOpen = null;

    /** @var bool Indica si se debe confirmar la eliminación */
    public bool $confirmDeletion = false;

    /** @var bool Indica si hay un error de validación */
    public bool $validationError = false;

    /** @var bool Indica si hay un éxito en el proceso */
    public bool $successProcess = false;

    /** @var array Valores por defecto para los campos del formulario */
    public array $defaultValues = [];

    // ===================== CONFIGURACIÓN DE CAST =====================

    /** @var bool Si se deben aplicar los casts definidos */
    protected bool $useFormCasts = true;

    /** @var array<string, string> Casts personalizados por campo */
    protected array $casts = [];

    // ===================== EVENTOS =====================

    /**
     * Eventos que este componente escucha.
     *
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        $tag = Str::studly((new ($this->model()))->getTagName());

        return [
            "edit{$tag}" => 'loadFormModel',
            "confirmDeletion{$tag}" => 'loadFormModelForDeletion',
        ];
    }

    // ===================== MÉTODOS ABSTRACTOS =====================

    /** @return class-string<Model> Modelo asociado */
    abstract protected function model(): string;

    /** @return array<string, mixed> Reglas de validación */
    abstract protected function dynamicRules(string $mode): array;

    /** @return string Ruta de la vista Blade del formulario */
    abstract protected function viewPath(): string;

    // ===================== CONFIGURACIÓN =====================

    /** @return array<string> Campos del modelo */
    protected function fields(): array
    {
        return (new ($this->model()))->getFillable();
    }

    /** @return array<string, mixed> Valores por defecto para los campos del formulario */
    protected function defaults(): array { return []; }

    /** @return array<string, mixed> Opciones del formulario */
    //protected function options(): array { return []; }

    /** @return array<string, mixed> Atributos personalizados para mensajes de validación */
    protected function attributes(): array { return []; }

    /** @return array<string, mixed> Mensajes personalizados de validación */
    protected function messages(): array { return []; }

    // ===================== CICLO DE VIDA =====================

    /** Inicializa el formulario. */
    public function mount(): void
    {
        $this->setupModelMetadata();
        $this->loadDefaults();
        //$this->loadOptions();
    }

    /** @return void Configura metadatos del modelo. */
    protected function setupModelMetadata(): void
    {
        $this->uniqueId = uniqid();

        $model = new ($this->model());

        $this->tagName      = $model->getTagName();
        $this->singularName = $model->getSingularName();
        $this->displayName  = $model->getDisplayName();

        $this->focusColumnOnOpen = $model->focusColumnOnOpen ?? null;

        $this->offcanvasId = 'offcanvas' . ucfirst(Str::camel($this->tagName));
        $this->formId      = Str::kebab($this->tagName) . '-form';
    }

    /** @return void Carga valores por defecto para los campos del formulario. */
    protected function loadDefaults(): void
    {
        $this->defaultValues = $this->defaults();
    }

    /** @return void Carga opciones del formulario. */
    /*
    protected function loadOptions(): void
    {
        foreach ($this->options() as $key => $value) {
            $this->$key = $value;
        }
    }
    */

    /** @return View Renderiza la vista del formulario. */
    public function render(): View
    {
        return view($this->viewPath());
    }

    // ===================== CARGA DE DATOS =====================

    /** @return void Carga el modelo del formulario. */
    public function loadFormModel(int $id): void
    {
        if ($this->loadData($id)) {
            $this->mode = 'edit';

            $this->dispatch($this->dispatchKey('refresh-offcanvas'));
        }
    }

    /** @return void Carga el modelo del formulario para eliminación. */
    public function loadFormModelForDeletion(int $id): void
    {
        if ($this->loadData($id)) {
            $this->mode = 'delete';
            $this->confirmDeletion = false;

            $this->dispatch($this->dispatchKey('refresh-offcanvas'));
        }
    }

    /** @return string Genera un identificador de evento. */
    protected function dispatchKey(string $key): string
    {
        $model = new ($this->model());

        return match ($key) {
            'refresh-offcanvas' => 'refresh-' . Str::kebab($model->getTagName()) . '-offcanvas',
            'reload-table' => 'reload-bt-' . Str::kebab($model->getTagName()) . 's',
            default => $key,
        };
    }

    /** @return bool Carga los datos del modelo. */
    protected function loadData(int $id): bool
    {
        $model = $this->model()::find($id);

        if ($model) {
            $data = $model->only(['id', ...$this->fields()]);

            $this->applyCasts($data);
            $this->fill($data);

            return true;
        }

        return false;
    }

    // ===================== CRUD =====================

    /** @return void Maneja el envío del formulario (create, update o delete). */
    public function onSubmit(): void
    {
        $this->successProcess  = false;
        $this->validationError = false;

        $this->mode = $this->mode ?: 'create';

        DB::beginTransaction();

        try {
            if ($this->mode === 'delete') {
                $this->delete();

            } else {
                $this->save();
            }

            DB::commit();

        } catch (ValidationException $e) {
            DB::rollBack();

            $this->setErrorBag($e->validator->errors());

            $this->handleException('danger', 'Error en la validación de los datos.');

        } catch (QueryException $e) {
            DB::rollBack();

            $this->handleException('danger', $this->parseDbError($e));

        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            $this->handleException('danger', 'Registro no encontrado.');

        } catch (Exception $e) {
            DB::rollBack();

            $this->handleException('danger', $e->getMessage());
        }
    }

    /** @return void Procesa el envío del formulario (create, update). */
    protected function save(): void
    {
        $validatedData = $this->validate(
            $this->dynamicRules($this->mode),
            $this->messages(),
            $this->attributes()
        );

        $this->convertEmptyValuesToNull($validatedData);
        $this->applyCasts($validatedData);

        $this->beforeSave($validatedData);
        $record = $this->model()::updateOrCreate(['id' => (int) $this->id], $validatedData);
        $this->afterSave($record);

        $this->handleSuccess('success', ucfirst($this->singularName) . " guardado correctamente.");
    }

    /** @return void Elimina el registro. */
    protected function delete(): void
    {
        $this->validate(
            $this->dynamicRules('delete'),
            $this->messages(),
            $this->attributes()
        );

        $record = $this->model()::findOrFail((int) $this->id);

        $this->beforeDelete($record);
        $record->delete();
        $this->afterDelete($record);

        $this->handleSuccess('warning', ucfirst($this->singularName) . " eliminado.");
    }

    // ===================== HOOKS =====================

    /** @param array $data Datos validados antes de guardar */
    protected function beforeSave(array &$data): void {}

    /** @param Model $record Registro guardado */
    protected function afterSave(Model $record): void {}

    /** @param Model $record Registro antes de ser eliminado */
    protected function beforeDelete(Model $record): void {}

    /** @param Model $record Registro eliminado */
    protected function afterDelete(Model $record): void {}

    // ===================== ERRORES Y NOTIFICACIONES =====================

    /** @return string Procesa un error de base de datos. */
    protected function parseDbError(QueryException $e): string
    {
        return match ($e->errorInfo[1]) {
            1452 => "Una clave foránea no es válida.",
            1062 => $this->extractDuplicateField($e->getMessage()),
            1451 => "No se puede eliminar: registro en uso.",
            default => env('APP_DEBUG') ? $e->getMessage() : "Error en base de datos."
        };
    }

    /** @return string Extrae el nombre del campo duplicado. */
    protected function extractDuplicateField(string $errorMessage): string
    {
        preg_match("/for key 'unique_(.*?)'/", $errorMessage, $matches);

        return isset($matches[1])
            ? "El valor ingresado para '" . str_replace('_', ' ', $matches[1]) . "' ya está en uso."
            : "Ya existe un registro con este valor.";
    }

    /** @return void Maneja un error. */
    protected function handleException(string $type, string $message, string $target = 'form', int $delay = 9000): void
    {
        $this->validationError = true;

        $this->dispatch($this->dispatchKey('refresh-offcanvas'));
        $this->dispatchNotification($type, $message, $target, $delay);
    }

    /** @return void Maneja un éxito. */
    protected function handleSuccess(string $type, string $message): void
    {
        $this->successProcess = true;

        $this->dispatch($this->dispatchKey('refresh-offcanvas'));
        $this->dispatch($this->dispatchKey('reload-table'));
        $this->dispatchNotification($type, $message, 'index');
    }

    /** @return void Envía una notificación. */
    protected function dispatchNotification(string $type, string $message, string $target = 'form', int $delay = 9000): void
    {
        $model = new ($this->model());
        $tag = ucfirst(Str::camel($model->getTagName()));

        $targets = [
            'form' => "#offcanvas{$tag} .notification-container",
            'index' => '#bt-' . Str::kebab($model->getTagName()) . 's .notification-container',
        ];

        $this->dispatch('notification',
            target: $targets[$target] ?? $targets['form'],
            type: $type,
            message: $message,
            delay: $delay
        );
    }

    // ===================== CASTEOS =====================

    /**
     * Convierte valores vacíos a null en los campos definidos en fields().
     *
     * @param array $data Datos a procesar
     * @return void
     */
    protected function convertEmptyValuesToNull(array &$data): void
    {
        foreach ($this->fields() as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
    }

    /**
     * Aplica casteos a los datos según $casts locales o del modelo.
     *
     * @param array $data
     */
    protected function applyCasts(array &$data): void
    {
        if (! $this->useFormCasts) return;

        $casts = $this->casts ?: (new ($this->model()))->getCasts();

        foreach ($data as $field => &$value) {
            if (isset($casts[$field])) {
                $value = $this->castValue($casts[$field], $value);
            }
        }
    }

    /** @return mixed Aplica un cast a un valor. */
    protected function castValue(string $type, mixed $value): mixed
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float'   => (float) $value,
            'string'  => (string) $value,
            'array'   => (array) $value,
            default   => $value,
        };
    }
}

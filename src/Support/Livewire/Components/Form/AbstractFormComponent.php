<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Support\Livewire\Components\Form;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Support\Str;

/**
 * Clase base moderna para formularios con Livewire.
 * Administra CRUD, validaciones, errores y notificaciones con una arquitectura limpia.
 *
 * @property string $uniqueId Identificador único del formulario
 * @property string $mode Modo actual del formulario ('create', 'edit', 'delete')
 * @property int|string|null $id ID del registro actual (puede ser null en modo create)
 * @property string $formId Identificador del formulario en el DOM
 * @property string $singularName Nombre singular del modelo asociado
 * @property string|false|null $focusColumnOnOpen Columna que recibe foco al abrir el formulario
 */
abstract class AbstractFormComponent extends Component
{
    // ===================== CONFIGURACIÓN DEL FORMULARIO =====================

    public string $uniqueId;
    public string $mode;
    public int|string|null $id = null;

    // ===================== METADATOS DEL MODELO =====================

    /** @var string ID del formulario */
    public string $formId;

    /** @var string Nombre singular del modelo */
    public string $singularName;

    /** @var string|false|null Columna que recibe foco al abrir el formulario */
    public string|false|null $focusColumnOnOpen = null;

    // ===================== CONFIGURACIÓN DE CAST =====================

    /** @var bool Si se deben aplicar los casts definidos */
    protected bool $useFormCasts = true;

    /** @var array<string, string> Casts personalizados por campo */
    protected array $casts = [];

    // ===================== MÉTODOS ABSTRACTOS =====================

    /** @return class-string<Model> Clase del modelo asociado */
    abstract protected function model(): string;

    /** @return string Ruta de la vista Blade del formulario */
    abstract protected function viewPath(): string;

    // ===================== CAMPOS DEL MODELO =====================

    /** @return array<string> Campos del modelo */
    protected function fields(): array
    {
        return (new ($this->model()))->getFillable();
    }

    /** @var array<string> Campos deshabilitados */
    protected array $disabledFields = [];

    // ===================== VALIDACIÓN =====================

    /** @return array Reglas generales de validación */
    protected function rules(): array { return []; }

    /** @param string $mode Modo del formulario */
    protected function dynamicRules(string $mode): array { return $this->rules(); }

    /** @return array Atributos personalizados para mensajes de validación */
    protected function attributes(): array { return []; }

    /** @return array Mensajes personalizados de validación */
    protected function messages(): array { return []; }

    // ===================== HOOKS =====================

    /** @param array $data Datos validados antes de guardar */
    protected function beforeSave(array &$data): void {}

    /** @param Model $record Registro guardado */
    protected function afterSave(Model $record): void {}

    /** @param Model $record Registro antes de ser eliminado */
    protected function beforeDelete(Model $record): void {}

    /** @param Model $record Registro eliminado */
    protected function afterDelete(Model $record): void {}

    // ===================== CICLO DE VIDA =====================

    /**
     * Inicializa el formulario.
     *
     * @param string $mode
     * @param int|string|null $id
     */
    public function mount(string $mode = 'create', int|string|null $id = null): void
    {
        $this->uniqueId = uniqid();
        $this->mode     = $mode;
        $this->id       = $id;

        if ($mode !== 'create' && $id) {
            $model = $this->model()::findOrFail($id);

            $this->fill($model->only($model->getFillable()));
        }

        $this->setupModelMetadata();
    }

    /** Carga metadatos del modelo para configurar el formulario. */
    protected function setupModelMetadata(): void
    {
        $model = new ($this->model());

        $this->formId            = Str::kebab($model->getTagName()) . '-form';
        $this->singularName      = $model->getSingularName();
        $this->focusColumnOnOpen = $model->focusColumnOnOpen ?? null;
    }

    // ===================== CRUD =====================

    /** Maneja el envío del formulario (create, update o delete). */
    public function onSubmit(): void
    {
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

    /** Guarda o actualiza el registro. */
    protected function save(): void
    {
        $rules = method_exists($this, 'dynamicRules')
            ? $this->dynamicRules($this->mode)
            : $this->rules();

        $validatedData = $this->validate($rules, $this->messages(), $this->attributes());

        $this->convertEmptyValuesToNull($validatedData);
        $this->applyCasts($validatedData);

        $this->beforeSave($validatedData);
        $record = $this->model()::updateOrCreate(['id' => (int) $this->id], $validatedData);
        $this->afterSave($record);

        $this->handleSuccess('success', ucfirst($this->singularName) . " guardado correctamente.");
    }

    /** Elimina el registro actual. */
    protected function delete(): void
    {
        $rules = method_exists($this, 'dynamicRules')
            ? $this->dynamicRules($this->mode)
            : $this->rules();

        $this->validate($rules, $this->messages(), $this->attributes());

        $record = $this->model()::findOrFail((int) $this->id);
        $this->beforeDelete($record);
        $record->delete();
        $this->afterDelete($record);

        $this->handleSuccess('warning', ucfirst($this->singularName) . " eliminado.");
    }

    // ===================== ERRORES Y NOTIFICACIONES =====================

    /**
     * Interpreta errores comunes de base de datos.
     *
     * @param QueryException $e
     * @return string
     */
    protected function parseDbError(QueryException $e): string
    {
        return match ($e->errorInfo[1]) {
            1452 => "Una clave foránea no es válida.",
            1062 => $this->extractDuplicateField($e->getMessage()),
            1451 => "No se puede eliminar: registro en uso.",
            default => env('APP_DEBUG') ? $e->getMessage() : "Error en base de datos."
        };
    }

    /**
     * Extrae el campo duplicado de un error MySQL.
     *
     * @param string $errorMessage
     * @return string
     */
    protected function extractDuplicateField(string $errorMessage): string
    {
        preg_match("/for key 'unique_(.*?)'/", $errorMessage, $matches);
        return isset($matches[1])
            ? "El valor ingresado para '" . str_replace('_', ' ', $matches[1]) . "' ya está en uso."
            : "Ya existe un registro con este valor.";
    }

    /**
     * Maneja errores comunes y muestra una notificación.
     *
     * @param string $type
     * @param string $message
     * @param string $target
     * @param int $delay
     */
    protected function handleException(string $type, string $message, string $target = 'form', int $delay = 9000): void
    {
        $this->dispatch($this->dispatchKey('refresh-form'));
        $this->dispatchNotification($type, $message, $target, $delay);
    }

    /**
     * Maneja una operación exitosa.
     *
     * @param string $type
     * @param string $message
     */
    protected function handleSuccess(string $type, string $message): void
    {
        if ($this->getRedirectRoute()) {
            $this->redirectRoute($this->getRedirectRoute());
            $this->dispatchNotification($type, $message, 'index');
        }

        $this->dispatchNotification($type, $message);
    }

    /**
     * Envía una notificación visual al frontend.
     *
     * @param string $type
     * @param string $message
     * @param string $target
     * @param int $delay
     */
    protected function dispatchNotification(string $type, string $message, string $target = 'form', int $delay = 9000): void
    {
        $model = new ($this->model());
        $tag = ucfirst(Str::camel($model->getTagName()));

        $targets = [
            'form'  => "#{$tag}Form .notification-container",
            'index' => "#bt-" . Str::kebab($model->getTagName()) . "s .notification-container",
        ];

        $this->dispatch('notification',
            target: $targets[$target] ?? $targets['form'],
            type: $type,
            message: $message,
            delay: $delay,
            deferReload: $target === 'index',
        );
    }

    /** Genera el nombre del evento para dispatch. */
    protected function dispatchKey(string $event): string
    {
        $model = new ($this->model());

        return match ($event) {
            'refresh-form' => 'refresh-' . Str::kebab($model->getTagName()) . '-form',
            default        => $event,
        };
    }

    /** Ruta de redirección luego de guardar o eliminar. */
    protected function getRedirectRoute(): ?string
    {
        return null;
    }

    /** Renderiza la vista del formulario. */
    public function render(): View
    {
        return view($this->viewPath());
    }

    // ===================== UTILIDADES =====================

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

    /**
     * Aplica casteo simple de tipo.
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
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

    // ===================== HELPERS =====================

    public function isFieldDisabled(string $field): bool
    {
        return in_array($field, $this->disabledFields, true);
    }

}

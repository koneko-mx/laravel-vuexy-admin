<?php

namespace Koneko\VuexyAdmin\Livewire\Form;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Clase base abstracta para manejar formularios de tipo Off-Canvas con Livewire.
 *
 * Esta clase proporciona métodos reutilizables para operaciones CRUD, validaciones dinámicas,
 * manejo de transacciones en base de datos y eventos de Livewire.
 *
 * @package Koneko\VuexyAdmin\Livewire\Form
 */
abstract class AbstractFormOffCanvasComponent extends Component
{
    /**
     * Identificador único del formulario, usado para evitar conflictos en instancias múltiples.
     *
     * @var string
     */
    public $uniqueId;

    /**
     * Modo actual del formulario: puede ser 'create', 'edit' o 'delete'.
     *
     * @var string
     */
    public $mode;

    /**
     * ID del registro que se está editando o eliminando.
     *
     * @var int|null
     */
    public $id;

    /**
     * Valores por defecto para los campos del formulario,
     *
     * @var array
     */
    public $defaultValues;

    /**
     * Nombre de la etiqueta para generar Componentes
     *
     * @var string
     */
    public $tagName;

    /**
     * Nombre de la columna que contiene el nombre del registro.
     *
     * @var string
     */
    public $columnNameLabel;

    /**
     * Nombre singular del modelo
     *
     * @var string
     */
    public $singularName;

    /*
     * Nombre del identificador del Canvas
     *
     * @var string
     */
    public $offcanvasId;

    /*
     * Nombre del identificador del Form
     *
     * @var string
     */
    public $formId;

    /**
     * Campo que se debe enfocar cuando se abra el formulario.
     *
     * @var string
     */
    public $focusOnOpen;

    /**
     * Indica si se desea confirmar la eliminación del registro.
     *
     * @var bool
     */
    public $confirmDeletion = false;

    /**
     * Indica si se ha producido un error de validación.
     *
     * @var bool
     */
    public $validationError = false;

    /*
     * Indica si se ha procesado correctamente el formulario.
     *
     * @var bool
     */
    public $successProcess = false;

    /**
     * Campos que deben ser casteados a tipos específicos.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    // ===================== MÉTODOS ABSTRACTOS =====================

    /**
     * Define el modelo Eloquent asociado con el formulario.
     *
     * @return string
     */
    abstract protected function model(): string;

    /**
     * Define reglas de validación dinámicas según el modo del formulario.
     *
     * @param string $mode Modo actual del formulario ('create', 'edit', 'delete').
     * @return array<string, mixed> Reglas de validación.
     */
    abstract protected function dynamicRules(string $mode): array;

    /**
     * Retorna la ruta de la vista asociada al formulario.
     *
     * @return string Ruta de la vista Blade.
     */
    abstract protected function viewPath(): string;

    // ===================== CONFIGURACIÓN =====================

    /**
     * Define los campos del formulario.
     *
     * @return array<string, mixed>
     */
    protected function fields(): array
    {
        return (new ($this->model()))->getFillable();
    }

    /**
     * Retorna los valores por defecto para los campos del formulario.
     *
     * @return array<string, mixed> Valores predeterminados.
     */
    protected function defaults(): array
    {
        return [];
    }

    /**
     * Campo que se debe enfocar cuando se abra el formulario.
     *
     * @return string
     */
    protected function focusOnOpen(): string
    {
        return '';
    }

    // ===================== OPCIONES =====================

    /**
     * Devuelve las opciones que se mostrarán en los selectores del formulario.
     *
     * @return array<string, mixed> Opciones para los campos del formulario.
     */
    protected function options(): array
    {
        return [];
    }

    // ===================== VALIDACIONES =====================

    protected function attributes(): array
    {
        return [];
    }

    protected function messages(): array
    {
        return [];
    }

    // ===================== INICIALIZACIÓN DEL COMPONENTE =====================

    /**
     * Se ejecuta cuando el componente se monta por primera vez.
     *
     * Inicializa propiedades y carga datos iniciales.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->uniqueId = uniqid();

        $model = new ($this->model());

        $this->tagName         = Str::camel($model->tagName);
        $this->columnNameLabel = $model->columnNameLabel;
        $this->singularName    = $model->singularName;
        $this->offcanvasId     = 'offcanvas' . ucfirst(Str::camel($model->tagName));
        $this->formId          = Str::camel($model->tagName)  .'Form';
        $this->focusOnOpen     = "{$this->focusOnOpen()}_{$this->uniqueId}";

        $this->loadDefaults();
        $this->loadOptions();
    }

    // ===================== INICIALIZACIÓN Y CONFIGURACIÓN =====================

    /**
     * Devuelve los valores por defecto para los campos del formulario.
     *
     * @return array<string, mixed> Valores por defecto.
     */
    private function loadDefaults(): void
    {
        $this->defaultValues = $this->defaults();
    }

    /**
     * Carga las opciones necesarias para los campos del formulario.
     *
     * @return void
     */
    private function loadOptions(): void
    {
        foreach ($this->options() as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Carga los datos de un modelo específico en el formulario para su edición.
     *
     * @param int $id ID del registro a editar.
     * @return void
     */
    public function loadFormModel(int $id): void
    {
        if ($this->loadData($id)) {
            $this->mode = 'edit';

            $this->dispatch($this->getDispatche('refresh-offcanvas'));
        }
    }

    /**
     * Carga el modelo para confirmar su eliminación.
     *
     * @param int $id ID del registro a eliminar.
     * @return void
     */
    public function loadFormModelForDeletion(int $id): void
    {
        if ($this->loadData($id)) {
            $this->mode = 'delete';
            $this->confirmDeletion = false;

            $this->dispatch($this->getDispatche('refresh-offcanvas'));
        }
    }

    private function getDispatche(string $name): string
    {
        $model = new ($this->model());

        $dispatches = [
            'refresh-offcanvas' => 'refresh-' . Str::kebab($model->tagName) . '-offcanvas',
            'reload-table'      => 'reload-bt-' . Str::kebab($model->tagName) . 's',
        ];

        return $dispatches[$name] ?? null;
    }


    /**
     * Carga los datos del modelo según el ID proporcionado.
     *
     * @param int $id ID del modelo.
     * @return bool True si los datos fueron cargados correctamente.
     */
    protected function loadData(int $id): bool
    {
        $model = $this->model()::find($id);

        if ($model) {

dd($this->fields());

            $data = $model->only(['id', ...$this->fields()]);

            $this->applyCasts($data);
            $this->fill($data);


            return true;
        }

        return false;
    }

    // ===================== OPERACIONES CRUD =====================

    /**
     * Método principal para enviar el formulario.
     *
     * @return void
     */
    public function onSubmit(): void
    {
        $this->successProcess  = false;
        $this->validationError = false;

        if(!$this->mode)
            $this->mode = 'create';

        DB::beginTransaction(); // Iniciar transacción

        try {
            if($this->mode === 'delete'){
                $this->delete();

            }else{
                $this->save();
            }

            DB::commit();

        } catch (ValidationException $e) {
            DB::rollBack();
            $this->handleValidationException($e);

        } catch (QueryException $e) {
            DB::rollBack();
            $this->handleDatabaseException($e);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $this->handleException('danger', 'Registro no encontrado.');

        } catch (Exception $e) {
            DB::rollBack(); // Revertir la transacción si ocurre un error
            $this->handleException('danger', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Guarda o actualiza un registro en la base de datos.
     *
     * @return void
     * @throws ValidationException
     */
    protected function save(): void
    {
        // Valida incluyendo atributos personalizados
        $validatedData = $this->validate(
            $this->dynamicRules($this->mode),
            $this->messages(),
            $this->attributes()
        );

        $this->convertEmptyValuesToNull($validatedData);
        $this->applyCasts($validatedData);

        $this->beforeSave($validatedData);
        $record = $this->model()::updateOrCreate(['id' => $this->id], $validatedData);
        $this->afterSave($record);

        $this->handleSuccess('success', ucfirst($this->singularName) . " guardado correctamente.");
    }

    /**
     * Elimina un registro en la base de datos.
     *
     * @return void
     */
    protected function delete(): void
    {
        $this->validate($this->dynamicRules(
            'delete',
            $this->messages(),
            $this->attributes()
        ));

        $record = $this->model()::findOrFail($this->id);

        $this->beforeDelete($record);
        $record->delete();
        $this->afterDelete($record);

        $this->handleSuccess('warning', ucfirst($this->singularName) . " eliminado.");
    }

    // ===================== HOOKS DE ACCIONES CRUD =====================

    /**
     * Hook que se ejecuta antes de guardar datos en la base de datos.
     *
     * Este método permite realizar modificaciones o preparar los datos antes de ser validados
     * y almacenados. Es útil para formatear datos, agregar valores calculados o realizar
     * operaciones previas a la persistencia.
     *
     * @param array $data Datos validados que se almacenarán. Se pasan por referencia,
     *                    por lo que cualquier cambio aquí afectará directamente los datos guardados.
     *
     * @return void
     */
    protected function beforeSave(array &$data): void {}

    /**
     * Hook que se ejecuta después de guardar o actualizar un registro en la base de datos.
     *
     * Ideal para ejecutar tareas posteriores al guardado, como enviar notificaciones,
     * registrar auditorías o realizar acciones en otros modelos relacionados.
     *
     * @param \Illuminate\Database\Eloquent\Model $record El modelo que fue guardado, conteniendo
     *                                                     los datos actualizados.
     *
     * @return void
     */
    protected function afterSave($record): void {}

    /**
     * Hook que se ejecuta antes de eliminar un registro de la base de datos.
     *
     * Permite validar si el registro puede ser eliminado o realizar tareas previas
     * como desasociar relaciones, eliminar archivos relacionados o verificar restricciones.
     *
     * @param \Illuminate\Database\Eloquent\Model $record El modelo que está por ser eliminado.
     *
     * @return void
     */
    protected function beforeDelete($record): void {}

    /**
     * Hook que se ejecuta después de eliminar un registro de la base de datos.
     *
     * Útil para realizar acciones adicionales tras la eliminación, como limpiar datos relacionados,
     * eliminar archivos vinculados o registrar eventos de auditoría.
     *
     * @param \Illuminate\Database\Eloquent\Model $record El modelo eliminado. Aunque ya no existe en la base de datos,
     *                                                     se conserva la información del registro en memoria.
     *
     * @return void
     */
    protected function afterDelete($record): void {}

    // ===================== MANEJO DE VALIDACIONES Y EXCEPCIONES =====================

    /**
     * Maneja las excepciones de validación.
     *
     * Este método captura los errores de validación, los agrega al error bag de Livewire
     * y dispara un evento para manejar el fallo de validación, útil en formularios modales.
     *
     * @param ValidationException $e Excepción de validación capturada.
     * @return void
     */
    protected function handleValidationException(ValidationException $e): void
    {
        $this->setErrorBag($e->validator->errors());

        // Notifica al usuario que ocurrió un error de validación
        $this->handleException('danger', 'Error en la validación de los datos.');
    }

    /**
     * Maneja las excepciones relacionadas con la base de datos.
     *
     * Analiza el código de error de la base de datos y genera un mensaje de error específico
     * para la situación. También se encarga de enviar una notificación de error.
     *
     * @param QueryException $e Excepción capturada durante la ejecución de una consulta.
     * @return void
     */
    protected function handleDatabaseException(QueryException $e): void
    {
        $errorMessage = match ($e->errorInfo[1]) {
            1452 => "Una clave foránea no es válida.",
            1062 => $this->extractDuplicateField($e->getMessage()),
            1451 => "No se puede eliminar el registro porque está en uso.",
            default => env('APP_DEBUG') ? $e->getMessage() : "Error inesperado en la base de datos.",
        };

        $this->handleException('danger', $errorMessage, 'form', 120000);
    }

    /**
     * Maneja cualquier tipo de excepción general y envía una notificación al usuario.
     *
     * @param string $type El tipo de notificación (success, danger, warning).
     * @param string $message El mensaje que se mostrará al usuario.
     * @param string $target El contenedor donde se mostrará la notificación (por defecto 'form').
     * @param int $delay Tiempo en milisegundos que durará la notificación en pantalla.
     * @return void
     */
    protected function handleException($type, $message, $target = 'form', $delay = 9000): void
    {
        $this->validationError = true;

        $this->dispatch($this->getDispatche('refresh-offcanvas'));
        $this->dispatchNotification($type, $message, $target, $delay);
    }

    /**
     * Extrae el nombre del campo duplicado de un error de base de datos MySQL.
     *
     * Esta función se utiliza para identificar el campo específico que causó un error
     * de duplicación de clave única, y genera un mensaje personalizado para el usuario.
     *
     * @param string $errorMessage El mensaje de error completo proporcionado por MySQL.
     * @return string Mensaje de error amigable para el usuario.
     */
    private function extractDuplicateField($errorMessage): string
    {
        preg_match("/for key 'unique_(.*?)'/", $errorMessage, $matches);

        return isset($matches[1])
            ? "El valor ingresado para '" . str_replace('_', ' ', $matches[1]) . "' ya está en uso."
            : "Ya existe un registro con este valor.";
    }

    // ===================== NOTIFICACIONES Y ÉXITO =====================

    /**
     * Despacha una notificación tras el éxito de una operación.
     *
     * @param string $type Tipo de notificación (success, warning, danger)
     * @param string $message Mensaje a mostrar.
     * @return void
     */
    protected function handleSuccess(string $type, string $message): void
    {
        $this->successProcess = true;

        $this->dispatch($this->getDispatche('refresh-offcanvas'));
        $this->dispatch($this->getDispatche('reload-table'));

        $this->dispatchNotification($type, $message, 'index');
    }

    /**
     * Envía una notificación al navegador.
     *
     * @param string $type Tipo de notificación (success, danger, etc.)
     * @param string $message Mensaje de la notificación
     * @param string $target Destino (form, index)
     * @param int $delay Duración de la notificación en milisegundos
     */
    protected function dispatchNotification($type, $message, $target = 'form', $delay = 9000): void
    {
        $model = new ($this->model());

        $this->tagName         = $model->tagName;
        $this->columnNameLabel = $model->columnNameLabel;
        $this->singularName    = $model->singularName;

        $tagOffcanvas = ucfirst(Str::camel($model->tagName));

        $targetNotifies = [
            "index" => '#bt-' . Str::kebab($model->tagName) . 's .notification-container',
            "form" => "#offcanvas{$tagOffcanvas} .notification-container",
        ];

        $this->dispatch(
            'notification',
            target: $target === 'index' ? $targetNotifies['index'] : $targetNotifies['form'],
            type: $type,
            message: $message,
            delay: $delay
        );
    }

    // ===================== FORMULARIO Y CONVERSIÓN DE DATOS =====================

    /**
     * Convierte los valores vacíos a `null` en los campos que son configurados como `nullable`.
     *
     * Esta función verifica las reglas de validación actuales y transforma todos los campos vacíos
     * en valores `null` si las reglas permiten valores nulos. Es útil para evitar insertar cadenas vacías
     * en la base de datos donde se espera un valor nulo.
     *
     * @param array $data Los datos del formulario que se deben procesar.
     * @return void
     */
    protected function convertEmptyValuesToNull(array &$data): void
    {
        $nullableFields = array_keys(array_filter($this->dynamicRules($this->mode), function ($rules) {
            return in_array('nullable', (array) $rules);
        }));

        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
    }

    /**
     * Aplica tipos de datos definidos en `$casts` a los campos del formulario.
     *
     * Esta función toma los datos de entrada y los transforma en el tipo de datos esperado según
     * lo definido en la propiedad `$casts`. Es útil para asegurar que los datos se almacenen en
     * el formato correcto, como convertir cadenas a números enteros o booleanos.
     *
     * @param array $data Los datos del formulario que necesitan ser casteados.
     * @return void
     */
    protected function applyCasts(array &$data): void
    {
        foreach ($this->casts as $field => $type) {
            if (array_key_exists($field, $data)) {
                $data[$field] = $this->castValue($type, $data[$field]);
            }
        }
    }

    /**
     * Castea un valor a su tipo de dato correspondiente.
     *
     * Convierte un valor dado al tipo especificado, manejando adecuadamente los valores vacíos
     * o nulos. También asegura que valores como `0` o `''` sean tratados correctamente
     * para evitar errores al almacenarlos en la base de datos.
     *
     * @param string $type El tipo de dato al que se debe convertir (`boolean`, `integer`, `float`, `string`, `array`).
     * @param mixed $value El valor que se debe castear.
     * @return mixed El valor convertido al tipo especificado.
     */
    protected function castValue($type, $value): mixed
    {
        // Convertir valores vacíos o cero a null si corresponde
        if (is_null($value) || $value === '' || $value === '0' || $value === 0.0) {
            return match ($type) {
                'boolean' => false, // No permitir null en booleanos
                'integer' => 0,     // Valor por defecto para enteros
                'float', 'double' => 0.0, // Valor por defecto para decimales
                'string' => "",     // Convertir cadena vacía en null
                'array' => [],      // Evitar null en arrays
                default => null,    // Valor por defecto para otros tipos
            };
        }

        // Castear el valor si no es null ni vacío
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'string' => (string) $value,
            'array' => (array) $value,
            default => $value,
        };
    }


    // ===================== RENDERIZACIÓN DE VISTA =====================

    /**
     * Renderiza la vista del formulario.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view($this->viewPath());
    }
}

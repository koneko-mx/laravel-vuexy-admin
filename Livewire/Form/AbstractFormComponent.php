<?php

namespace Koneko\VuexyAdmin\Livewire\Form;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Class AbstractFormComponent
 *
 * Clase base y abstracta para la creación de formularios con Livewire.
 * Proporciona métodos y un flujo general para manejar operaciones CRUD
 * (creación, edición y eliminación), validaciones, notificaciones y
 * administración de errores en un entorno transaccional.
 *
 * @package Koneko\VuexyAdmin\Livewire\Form
 */
abstract class AbstractFormComponent extends Component
{
    /**
     * Identificador único del formulario, útil para distinguir múltiples instancias.
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
     * Texto que se mostrará en el botón de envío. Se adapta
     * automáticamente en función del modo actual (crear, editar o eliminar).
     *
     * @var string
     */
    public $btnSubmitText;

    /**
     * ID del registro que se está editando o eliminando.
     * Si el formulario está en modo 'create', puede ser null.
     *
     * @var int|null
     */
    public $id;

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

    // ======================================================================
    //                            MÉTODOS ABSTRACTOS
    // ======================================================================

    /**
     * Retorna la clase (namespace) del modelo Eloquent asociado al formulario.
     *
     * @return string
     */
    abstract protected function model(): string;

    /**
     * Retorna las reglas de validación de forma dinámica, dependiendo del modo del formulario.
     *
     * @param string $mode El modo actual del formulario (por ejemplo, 'create', 'edit' o 'delete').
     * @return array Reglas de validación (similares a las usadas en un Request de Laravel).
     */
    abstract protected function dynamicRules(string $mode): array;

    /**
     * Inicializa los datos del formulario con base en el registro (si existe)
     * y en el modo actual. Útil para prellenar campos en modo 'edit'.
     *
     * @param mixed  $record El registro encontrado, o null si se crea uno nuevo.
     * @param string $mode   El modo actual del formulario.
     * @return void
     */
    abstract protected function initializeFormData(mixed $record, string $mode): void;

    /**
     * Prepara los datos ya validados para ser guardados en base de datos.
     * Permite, por ejemplo, castear valores o limpiar ciertos campos.
     *
     * @param array $validatedData Datos que ya pasaron la validación.
     * @return array Datos listos para el almacenamiento (por ejemplo, en create o update).
     */
    abstract protected function prepareData(array $validatedData): array;

    /**
     * Define los contenedores de destino para las notificaciones.
     *
     * Retorna un array con keys como 'form', 'index', etc., y sus
     * valores deben ser selectores o identificadores en la vista, donde
     * se inyectarán las notificaciones.
     *
     * @return array
     */
    abstract protected function targetNotifies(): array;

    /**
     * Retorna la ruta de la vista Blade correspondiente a este formulario.
     *
     * Por ejemplo: 'package::livewire.some-form'.
     *
     * @return string
     */
    abstract protected function viewPath(): string;

    // ======================================================================
    //                         MÉTODOS DE VALIDACIÓN
    // ======================================================================

    /**
     * Retorna un array que define nombres de atributos personalizados para los mensajes de validación.
     *
     * @return array
     */
    protected function attributes(): array
    {
        return [];
    }

    /**
     * Retorna un array con mensajes de validación personalizados.
     *
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

    // ======================================================================
    //                   INICIALIZACIÓN Y CICLO DE VIDA
    // ======================================================================

    /**
     * Método que se ejecuta al montar (instanciar) el componente Livewire.
     * Inicializa propiedades clave como el $mode, $id, $uniqueId, el texto
     * del botón de envío, y carga datos del registro si no es un 'create'.
     *
     * @param string    $mode Modo del formulario: 'create', 'edit' o 'delete'.
     * @param int|null  $id   ID del registro a editar/eliminar (o null para crear).
     * @return void
     */
    public function mount(string $mode = 'create', mixed $id = null): void
    {
        $this->uniqueId = uniqid();
        $this->mode = $mode;
        $this->id   = $id;

        $model = new ($this->model());

        $this->tagName         = $model->tagName;
        $this->columnNameLabel = $model->columnNameLabel;
        $this->singularName    = $model->singularName;
        $this->formId          = Str::camel($model->tagName)  .'Form';

        $this->setBtnSubmitText();

        if ($this->mode !== 'create' && $this->id) {
            // Si no es modo 'create', cargamos el registro desde la BD
            $record = $this->model()::findOrFail($this->id);

            $this->initializeFormData($record, $mode);

        } else {
            // Modo 'create', o sin ID: iniciamos datos vacíos
            $this->initializeFormData(null, $mode);
        }
    }

    /**
     * Configura el texto del botón principal de envío, basado en la propiedad $mode.
     *
     * @return void
     */
    private function setBtnSubmitText(): void
    {
        $this->btnSubmitText = match ($this->mode) {
            'create' => 'Crear ' . $this->singularName(),
            'edit'   => 'Guardar cambios',
            'delete' => 'Eliminar ' . $this->singularName(),
            default  => 'Enviar'
        };
    }

    /**
     * Retorna el "singularName" definido en el modelo asociado.
     * Permite también decidir si se devuelve con la primera letra en mayúscula
     * o en minúscula.
     *
     * @param string $type Puede ser 'uppercase' o 'lowercase'. Por defecto, 'lowercase'.
     * @return string Nombre en singular del modelo, formateado.
     */
    private function singularName($type = 'lowercase'): string
    {
        /** @var Model $model */
        $model = new ($this->model());

        return $type === 'uppercase'
            ? ucfirst($model->singularName)
            : lcfirst($model->singularName);
    }

    /**
     * Método del ciclo de vida de Livewire que se llama en cada hidratación.
     * Puedes disparar eventos o manejar lógica que suceda en cada request
     * una vez que Livewire 'rehidrate' el componente en el servidor.
     *
     * @return void
     */
    public function hydrate(): void
    {
        $this->dispatch($this->dispatches()['on-hydrate']);
    }

    // ======================================================================
    //                           OPERACIONES CRUD
    // ======================================================================

    /**
     * Método principal de envío del formulario (submit). Gestiona los flujos
     * de crear, editar o eliminar un registro dentro de una transacción de BD.
     *
     * @return void
     */
    public function onSubmit(): void
    {
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
            $this->handleValidationException($e);

        } catch (QueryException $e) {
            DB::rollBack();
            $this->handleDatabaseException($e);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            $this->handleException('danger', 'Registro no encontrado.');

        } catch (Exception $e) {
            DB::rollBack();
            $this->handleException('danger', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }

    /**
     * Crea o actualiza un registro en la base de datos,
     * aplicando validaciones y llamadas a hooks antes y después de guardar.
     *
     * @return void
     * @throws ValidationException
     */
    protected function save(): void
    {
        // Validamos los datos, con posibles atributos y mensajes personalizados
        $validatedData = $this->validate(
            $this->dynamicRules($this->mode),
            $this->messages(),
            $this->attributes()
        );

        // Hook previo (por referencia)
        $this->beforeSave($validatedData);

        // Ajustamos/convertimos los datos finales
        $data   = $this->prepareData($validatedData);
        $record = $this->model()::updateOrCreate(['id' => $this->id], $data);

        // Hook posterior
        $this->afterSave($record);

        // Notificamos éxito
        $this->handleSuccess('success', $this->singularName('uppercase') . " guardado correctamente.");
    }

    /**
     * Elimina un registro de la base de datos (modo 'delete'),
     * aplicando validaciones y hooks antes y después de la eliminación.
     *
     * @return void
     * @throws ValidationException
     */
    protected function delete(): void
    {
        $this->validate($this->dynamicRules('delete', $this->messages(), $this->attributes()));

        $record = $this->model()::findOrFail($this->id);

        // Hook antes de la eliminación
        $this->beforeDelete($record);

        $record->delete();

        // Hook después de la eliminación
        $this->afterDelete($record);

        $this->handleSuccess('warning', $this->singularName('uppercase') . " eliminado.");
    }

    // ======================================================================
    //                           HOOKS DE ACCIONES
    // ======================================================================

    /**
     * Hook que se ejecuta antes de guardar o actualizar un registro.
     * Puede usarse para ajustar o limpiar datos antes de la operación en base de datos.
     *
     * @param array $data Datos validados que se van a guardar.
     *                    Se pasa por referencia para permitir cambios.
     * @return void
     */
    protected function beforeSave(array &$data): void {}

    /**
     * Hook que se ejecuta después de guardar o actualizar un registro.
     * Puede usarse para acciones como disparar eventos, notificaciones a otros sistemas, etc.
     *
     * @param mixed $record Instancia del modelo recién creado o actualizado.
     * @return void
     */
    protected function afterSave($record): void {}

    /**
     * Hook que se ejecuta antes de eliminar un registro.
     * Puede emplearse para validaciones adicionales o limpieza de datos relacionados.
     *
     * @param mixed $record Instancia del modelo que se eliminará.
     * @return void
     */
    protected function beforeDelete($record): void {}

    /**
     * Hook que se ejecuta después de eliminar un registro.
     * Útil para operaciones finales, como remover archivos relacionados o
     * disparar un evento de "elemento eliminado".
     *
     * @param mixed $record Instancia del modelo que se acaba de eliminar.
     * @return void
     */
    protected function afterDelete($record): void {}

    // ======================================================================
    //                   MANEJO DE VALIDACIONES Y ERRORES
    // ======================================================================

    /**
     * Maneja las excepciones de validación (ValidationException).
     * Asigna los errores al error bag de Livewire y muestra notificaciones.
     *
     * @param ValidationException $e Excepción de validación.
     * @return void
     */
    protected function handleValidationException(ValidationException $e): void
    {
        $this->setErrorBag($e->validator->errors());
        $this->handleException('danger', 'Error en la validación de los datos.');
        $this->dispatch($this->dispatches()['on-failed-validation']);
    }

    /**
     * Maneja las excepciones de base de datos (QueryException).
     * Incluye casos especiales para claves foráneas y duplicadas.
     *
     * @param QueryException $e Excepción de consulta a la base de datos.
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
     * Maneja excepciones o errores generales, mostrando una notificación al usuario.
     *
     * @param string $type    Tipo de notificación (por ejemplo, 'success', 'warning', 'danger').
     * @param string $message Mensaje que se mostrará en la notificación.
     * @param string $target  Objetivo/área donde se mostrará la notificación ('form', 'index', etc.).
     * @param int    $delay   Tiempo en milisegundos que la notificación permanecerá visible.
     * @return void
     */
    protected function handleException($type, $message, $target = 'form', $delay = 9000): void
    {
        $this->dispatchNotification($type, $message, $target, $delay);
    }

    /**
     * Extrae el campo duplicado de un mensaje de error MySQL, para mostrar un mensaje amigable.
     *
     * @param string $errorMessage Mensaje de error completo de la base de datos.
     * @return string Mensaje simplificado indicando cuál campo está duplicado.
     */
    private function extractDuplicateField($errorMessage): string
    {
        preg_match("/for key 'unique_(.*?)'/", $errorMessage, $matches);

        return isset($matches[1])
            ? "El valor ingresado para '" . str_replace('_', ' ', $matches[1]) . "' ya está en uso."
            : "Ya existe un registro con este valor.";
    }

    // ======================================================================
    //              NOTIFICACIONES Y REDIRECCIONAMIENTOS
    // ======================================================================

    /**
     * Maneja el flujo de notificación y redirección cuando una operación
     * (guardar, eliminar) finaliza satisfactoriamente.
     *
     * @param string $type    Tipo de notificación ('success', 'warning', etc.).
     * @param string $message Mensaje a mostrar.
     * @return void
     */
    protected function handleSuccess($type, $message): void
    {
        $this->dispatchNotification($type, $message, 'index');
        $this->redirectRoute($this->getRedirectRoute());
    }

    /**
     * Envía una notificación al navegador (mediante eventos de Livewire)
     * indicando el tipo, el mensaje y el destino donde debe visualizarse.
     *
     * @param string $type    Tipo de notificación (success, danger, etc.).
     * @param string $message Mensaje de la notificación.
     * @param string $target  Destino para mostrarla ('form', 'index', etc.).
     * @param int    $delay   Duración de la notificación en milisegundos.
     * @return void
     */
    protected function dispatchNotification($type, $message, $target = 'form', $delay = 9000): void
    {
        $this->dispatch(
            $target == 'index' ? 'store-notification' : 'notification',
            target: $target === 'index' ? $this->targetNotifies()['index'] : $this->targetNotifies()['form'],
            type: $type,
            message: $message,
            delay: $delay
        );
    }

    // ======================================================================
    //                            RENDERIZACIÓN
    // ======================================================================

    /**
     * Renderiza la vista Blade asociada a este componente.
     * Retorna un objeto Illuminate\View\View.
     *
     * @return View
     */
    public function render(): View
    {
        return view($this->viewPath());
    }
}

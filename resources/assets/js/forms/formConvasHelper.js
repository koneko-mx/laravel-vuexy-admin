/**
 * FormCanvasHelper
 *
 * Clase para orquestar la interacción entre un formulario dentro de un Offcanvas
 * de Bootstrap y el estado de Livewire (modo create/edit/delete), además de
 * manipular ciertos componentes externos como Select2.
 *
 * Se diseñó teniendo en cuenta que el DOM del Offcanvas puede reconstruirse
 * (re-render) de manera frecuente, por lo que muchos getters reacceden al DOM
 * dinámicamente.
 */
export default class FormCanvasHelper {
    /**
     * @param {string} offcanvasId       - ID del elemento Offcanvas en el DOM.
     * @param {object} liveWireInstance  - Instancia de Livewire asociada al formulario.
     */
    constructor(offcanvasId, liveWireInstance) {
        this.offcanvasId = offcanvasId;
        this.liveWireInstance = liveWireInstance;

        // Validamos referencias mínimas para evitar errores tempranos
        // Si alguna falta, se mostrará un error en consola.
        this.validateInitialDomRefs();
    }

    /**
     * Verifica la existencia básica de elementos en el DOM.
     * Muestra errores en consola si faltan elementos críticos.
     */
    validateInitialDomRefs() {
        const offcanvasEl = document.getElementById(this.offcanvasId);

        if (!offcanvasEl) {
            console.error(`❌ No se encontró el contenedor Offcanvas con ID: ${this.offcanvasId}`);
            return;
        }

        const formEl = offcanvasEl.querySelector('form');
        if (!formEl) {
            console.error(`❌ No se encontró el formulario dentro de #${this.offcanvasId}`);
            return;
        }

        const offcanvasTitle = offcanvasEl.querySelector('.offcanvas-title');
        const submitButtons = formEl.querySelectorAll('.btn-submit');
        const resetButtons = formEl.querySelectorAll('.btn-reset');

        if (!offcanvasTitle || !submitButtons.length || !resetButtons.length) {
            console.error(`❌ Faltan el título, botones de submit o reset dentro de #${this.offcanvasId}`);
        }
    }

    /**
     * Getter para el contenedor Offcanvas actual.
     * Retorna siempre la referencia más reciente del DOM.
     */
    get offcanvasEl() {
        return document.getElementById(this.offcanvasId);
    }

    /**
     * Getter para el formulario dentro del Offcanvas.
     */
    get formEl() {
        return this.offcanvasEl?.querySelector('form') ?? null;
    }

    /**
     * Getter para el título del Offcanvas.
     */
    get offcanvasTitleEl() {
        return this.offcanvasEl?.querySelector('.offcanvas-title') ?? null;
    }

    /**
     * Getter para la instancia de Bootstrap Offcanvas.
     * Siempre retorna la instancia más reciente en caso de re-render.
     */
    get offcanvasInstance() {
        if (!this.offcanvasEl) return null;
        return bootstrap.Offcanvas.getOrCreateInstance(this.offcanvasEl);
    }

    /**
     * Retorna todos los botones de submit en el formulario.
     */
    get submitButtons() {
        return this.formEl?.querySelectorAll('.btn-submit') ?? [];
    }

    /**
     * Retorna todos los botones de reset en el formulario.
     */
    get resetButtons() {
        return this.formEl?.querySelectorAll('.btn-reset') ?? [];
    }

    /**
     * Método principal para manejar la recarga del Offcanvas según los estados en Livewire.
     * Se encarga de resetear el formulario, limpiar errores y cerrar/abrir el Offcanvas
     * según sea necesario.
     *
     * @param {string|null} triggerMode - Forzar la acción (e.g., 'reset', 'create'). Si no se especifica, se verifica según Livewire.
     */
    reloadOffcanvas(triggerMode = null) {
        setTimeout(() => {
            const mode = this.liveWireInstance.get('mode');
            const successProcess  = this.liveWireInstance.get('successProcess');
            const validationError = this.liveWireInstance.get('validationError');

            // Si se completa la acción o triggerMode = 'reset',
            // reseteamos completamente y cerramos el Offcanvas.
            if (triggerMode === 'reset' || successProcess) {
                this.resetFormAndState('create');

                return;
            }

            // Forzar modo create si se solicita explícitamente
            if (triggerMode === 'create') {
                // Evitamos re-reset si ya estamos en 'create'
                if (mode === 'create') return;

                this.resetFormAndState('create');

                this.focusOnOpen();

                return;
            }

            // Si no, simplemente preparamos la UI según el modo actual.
            this.prepareOffcanvasUI(mode);

            // Si hay errores de validación, reabrimos el Offcanvas para mostrarlos.
            if (validationError) {
                this.liveWireInstance.set('validationError', null, false);

                return;
            }

            // Si estamos en edit o delete, solo abrimos el Offcanvas.
            if (mode === 'edit' || mode === 'delete') {
                this.clearErrors();

                if(mode === 'edit') {
                    this.focusOnOpen();
                }

                return;
            }
        }, 20);
    }

    /**
     * Reabre o fuerza la apertura del Offcanvas si hay errores de validación
     * o si el modo de Livewire es 'edit' o 'delete'.
     *
     * Normalmente se llama cuando hay un dispatch/evento de Livewire,
     * por ejemplo si el servidor devuelve un error de validación (para mostrarlo)
     * o si se acaba de cargar un registro para editar o eliminar.
     *
     * - Si hay `validationError`, forzamos la reapertura con `toggleOffcanvas(true, true)`
     *   para que se refresque correctamente y el usuario vea los errores.
     * - Si el modo es 'edit' o 'delete', simplemente mostramos el Offcanvas sin forzar
     *   un refresco de la interfaz.
     */
    refresh() {
        setTimeout(() => {
            const mode = this.liveWireInstance.get('mode');
            const successProcess  = this.liveWireInstance.get('successProcess');
            const validationError = this.liveWireInstance.get('validationError');

            // cerramos el Offcanvas.
            if (successProcess) {
                this.toggleOffcanvas(false);

                this.resetFormAndState('create');

                return;
            }


            if (validationError) {
                // Forzamos la reapertura para que se rendericen
                this.toggleOffcanvas(true, true);

                return;
            }

            if (mode === 'edit' || mode === 'delete') {
                // Abrimos el Offcanvas para edición o eliminación
                this.toggleOffcanvas(true);

                return;
            }
        }, 10);
    }


    /**
     * Prepara la UI del Offcanvas según el modo actual: cambia texto de botones, título,
     * habilita o deshabilita campos, etc.
     *
     * @param {string} mode - Modo actual en Livewire: 'create', 'edit' o 'delete'
     */
    prepareOffcanvasUI(mode) {
        // Configura el texto y estilo de botones
        this.configureButtons(mode);

        // Ajusta el título del Offcanvas
        this.configureTitle(mode);

        // Activa o desactiva campos según el modo
        this.configureReadonlyMode(mode === 'delete');
    }

    /**
     * Cierra o muestra el Offcanvas.
     *
     * @param {boolean} show  - true para mostrar, false para ocultar.
     * @param {boolean} force - true para forzar el refresco rápido del Offcanvas.
     */
    toggleOffcanvas(show = false, force = false) {
        const instance = this.offcanvasInstance;

        if (!instance) return;

        if (show) {
            if (force) {
                // "Force" hace un hide + show para asegurar un nuevo render
                instance.hide();
                setTimeout(() => instance.show(), 10);

            } else {
                instance.show();
            }

        } else {
            instance.hide();
        }
    }

    /**
     * Resetea el formulario y el estado en Livewire (modo, id, errores).
     *
     * @param {string} targetMode - Modo al que queremos resetear, típicamente 'create'.
     */
    resetFormAndState(targetMode) {
        if (!this.formEl) return;

        // Restablecemos en Livewire
        this.liveWireInstance.set('successProcess', null, false);
        this.liveWireInstance.set('validationError', null, false);
        this.liveWireInstance.set('mode', targetMode, false);
        this.liveWireInstance.set('id', null, false);

        // Limpiamos el formulario
        this.formEl.reset();
        this.clearErrors();

        // Restablecemos valores por defecto del formulario
        const defaults = this.liveWireInstance.get('defaultValues');
        if (defaults && typeof defaults === 'object') {
            Object.entries(defaults).forEach(([key, value]) => {
                this.liveWireInstance.set(key, value, false);
            });
        }

        // Limpiar select2 automáticamente
        $(this.formEl)
            .find('select.select2-hidden-accessible')
            .each(function () {
                $(this).val(null).trigger('change');
            });

        // Desactivamos el modo lectura
        this.configureReadonlyMode(false);

        // Reconfiguramos el Offcanvas UI
        this.prepareOffcanvasUI(targetMode);
    }

    /**
     * Configura el texto y estilo de los botones de submit y reset
     * según el modo de Livewire.
     *
     * @param {string} mode - 'create', 'edit' o 'delete'
     */
    configureButtons(mode) {
        const singularName = this.liveWireInstance.get('singularName');

        // Limpiar clases previas
        this.submitButtons.forEach(button => {
            button.classList.remove('btn-danger', 'btn-primary');
        });
        this.resetButtons.forEach(button => {
            button.classList.remove('btn-text-secondary', 'btn-label-secondary');
        });

        // Configurar botón de submit según el modo
        this.submitButtons.forEach(button => {
            switch (mode) {
                case 'create':
                    button.classList.add('btn-primary');
                    button.textContent = `Crear ${singularName.toLowerCase()}`;
                    break;
                case 'edit':
                    button.classList.add('btn-primary');
                    button.textContent = `Guardar cambios`;
                    break;
                case 'delete':
                    button.classList.add('btn-danger');
                    button.textContent = `Eliminar ${singularName.toLowerCase()}`;
                    break;
            }
        });

        // Configurar botones de reset según el modo
        this.resetButtons.forEach(button => {
            // Cambia la clase dependiendo si se trata de un modo 'delete' o no
            const buttonClass = (mode === 'delete') ? 'btn-text-secondary' : 'btn-label-secondary';
            button.classList.add(buttonClass);
        });
    }

    /**
     * Ajusta el título del Offcanvas según el modo y la propiedad configurada en Livewire.
     *
     * @param {string} mode - 'create', 'edit' o 'delete'
     */
    configureTitle(mode) {
        if (!this.offcanvasTitleEl) return;

        const capitalizeFirstLetter =(str) => {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        const singularName    = this.liveWireInstance.get('singularName');
        const columnNameLabel = this.liveWireInstance.get('columnNameLabel');
        const editName        = this.liveWireInstance.get(columnNameLabel);

        switch (mode) {
            case 'create':
                this.offcanvasTitleEl.innerHTML = `<i class="ti ti-plus ml-2"></i> ${capitalizeFirstLetter(singularName)} `;
                break;
            case 'edit':
                this.offcanvasTitleEl.innerHTML = `${editName} <i class="ti ti-lg ti-pencil ml-2 text-success"></i>`;
                break;
            case 'delete':
                this.offcanvasTitleEl.innerHTML = `${editName} <i class="ti ti-lg ti-eraser ml-2 text-danger"></i>`;
                break;
        }
    }

    /**
     * Configura el modo de solo lectura/edición en los campos del formulario.
     * Deshabilita inputs y maneja el "readonly" en checkboxes/radios.
     *
     * @param {boolean} readOnly - true si queremos modo lectura, false para edición.
     */
    configureReadonlyMode(readOnly) {
        if (!this.formEl) return;

        const inputs = this.formEl.querySelectorAll('input, textarea, select');

        inputs.forEach(el => {
            // Saltar campos marcados como "data-always-enabled"
            if (el.hasAttribute('data-always-enabled')) return;

            // Para selects
            if (el.tagName === 'SELECT') {
                if ($(el).hasClass('select2-hidden-accessible')) {
                    // Deshabilitar select2
                    $(el).prop('disabled', readOnly).trigger('change.select2');
                } else {
                    this.toggleSelectReadonly(el, readOnly);
                }
                return;
            }

            // Para checkboxes / radios
            if (el.type === 'checkbox' || el.type === 'radio') {
                this.toggleCheckboxReadonly(el, readOnly);
                return;
            }

            // Para inputs de texto / textarea
            el.readOnly = readOnly;
        });
    }

    /**
     * Alterna modo "readonly" en un checkbox/radio simulando la inhabilitación
     * sin marcarlo como 'disabled' (para mantener su apariencia).
     *
     * @param {HTMLElement} checkbox - Elemento checkbox o radio.
     * @param {boolean} enabled      - true si se quiere modo lectura, false en caso contrario.
     */
    toggleCheckboxReadonly(checkbox, enabled) {
        if (enabled) {
            checkbox.setAttribute('readonly-mode', 'true');
            checkbox.style.pointerEvents = 'none';
            checkbox.onclick = function (event) {
                event.preventDefault();
            };
        } else {
            checkbox.removeAttribute('readonly-mode');
            checkbox.style.pointerEvents = '';
            checkbox.onclick = null;
        }
    }

    /**
     * Alterna modo "readonly" para un <select> convencional.
     *
     * @param {HTMLElement} select - Elemento select.
     * @param {boolean} enabled    - true si queremos readonly, false si editable.
     */
    toggleSelectReadonly(select, enabled) {
        if (enabled) {
            select.setAttribute('readonly-mode', 'true');
            select.style.pointerEvents = 'none';
            select.tabIndex = -1;
        } else {
            select.removeAttribute('readonly-mode');
            select.style.pointerEvents = '';
            select.tabIndex = '';
        }
    }

    /**
     * Hace focus en el elemento con el selector dado.
     */
    focusOnOpen() {
        const focusSelector = this.liveWireInstance.get('focusOnOpen'); // Obtiene el selector de Livewire

        if (!focusSelector) return;

        setTimeout(() => {
            // Buscar el elemento real en el DOM
            const focusElement = document.getElementById(focusSelector);

            // Si existe, hacer focus
            if (focusElement) {
                focusElement.focus();
            } else {
                console.warn(`Elemento no encontrado: ${focusSelector}`);
            }
        }, 250);
    }

    /**
     * Limpia mensajes de error y la clase 'is-invalid' en el formulario.
     */
    clearErrors() {
        if (!this.formEl) return;

        // Remover mensajes de error en texto
        this.formEl.querySelectorAll('.text-danger').forEach(el => el.remove());

        // Remover la clase 'is-invalid' de los inputs afectados
        this.formEl.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        // Remover las notificaciones
        this.formEl.querySelectorAll('.notification-container').forEach(el => el.innerHTML = '');

        // Removemos el checkbox de confirmación de eliminar
        const confirmDeletion = this.formEl.querySelector('.confirm-deletion');

        if (confirmDeletion) {
            confirmDeletion.remove();
        }
    }
}

// Exponemos la clase en window para acceso global (si fuese necesario)
window.FormCanvasHelper = FormCanvasHelper;

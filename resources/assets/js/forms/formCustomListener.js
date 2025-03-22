export default class FormCustomListener {
    constructor(config = {}) {
        const defaultConfig = {
            formSelector: '.form-custom-listener', // Selector para formularios
            buttonSelectors: [], // Selectores específicos para botones
            callbacks: [], // Callbacks correspondientes a los botones específicos
            allowedInputTags: ['INPUT', 'SELECT', 'TEXTAREA'], // Tags permitidos para cambios
            validationConfig: null, // Nueva propiedad para la configuración de validación
            dispatchOnSubmit: null // Callback Livewire para disparar al enviar el formulario
        };

        this.config = { ...defaultConfig, ...config };

        // Aseguramos que los métodos que dependen de `this` estén vinculados al contexto correcto
        this.defaultButtonHandler = this.defaultButtonHandler.bind(this);
        this.formValidationInstance = null;

        this.initForms();
    }

    /**
     * Inicializa los formularios encontrados en el DOM.
     */
    initForms() {
        const forms = document.querySelectorAll(this.config.formSelector);

        if (forms.length === 0) {
            console.error(`No se encontraron formularios con el selector ${this.config.formSelector}.`);
            return;
        }

        forms.forEach(form => {
            if (form.dataset.initialized === 'true') {
                console.warn(`Formulario ya inicializado: ${form}`);
                return;
            }

            this.initFormEvents(form);

            // Si se pasó configuración de validación, inicialízala
            if (this.config.validationConfig) {
                this.initializeValidation(form);
            }

            form.dataset.initialized = 'true'; // Marcar formulario como inicializado
        });
    }

    /**
     * Configura los eventos para un formulario individual.
     * @param {HTMLElement} form - El formulario que será manejado.
     */
    initFormEvents(form) {
        const buttons = this.getButtons(form);

        buttons.forEach(({ button, callback }, index) => {
            if (button) {
                button.addEventListener('click', () => {
                    this.handleButtonClick(index, form, buttons, callback);
                });
            }
        });

        form.addEventListener('input', event =>
            this.handleInputChange(
                event,
                form,
                buttons.map(b => b.button)
            )
        );
    }

    /**
     * Obtiene los botones y sus callbacks según la configuración.
     * @param {HTMLElement} form - El formulario del cual obtener botones.
     * @returns {Array} Array de objetos con { button, callback }.
     */
    getButtons(form) {
        const buttons = [];

        this.config.buttonSelectors.forEach((selector, index) => {
            const buttonList = Array.from(form.querySelectorAll(selector));
            const callback = this.config.callbacks[index];

            buttonList.forEach(button => {
                buttons.push({ button, callback });
            });
        });

        return buttons;
    }

    /**
     * Maneja los cambios en los campos de entrada.
     * @param {Event} event - El evento del cambio.
     * @param {HTMLElement} form - El formulario actual.
     * @param {HTMLElement[]} buttons - Array de botones en el formulario.
     */
    handleInputChange(event, form, buttons) {
        const target = event.target;

        if (['INPUT', 'SELECT', 'TEXTAREA'].includes(target.tagName)) {
            this.toggleButtonsState(buttons, true);
        }
    }

    /**
     * Maneja el clic en un botón específico.
     * @param {number} index - Índice del botón.
     * @param {HTMLElement} form - El formulario actual.
     * @param {Array} buttons - Array de objetos { button, callback }.
     * @param {function|null} callback - Callback definido para el botón.
     */
    handleButtonClick(index, form, buttons, callback) {
        if (typeof callback === 'function') {
            callback(
                form,
                buttons[index].button,
                buttons.map(b => b.button)
            );
        } else {
            this.defaultButtonHandler(
                form,
                buttons[index].button,
                buttons.map(b => b.button)
            );
        }
    }

    /**
     * Maneja la acción cuando el formulario es válido.
     * Este método puede ser sobreescrito para personalizar el comportamiento.
     */
    handleFormValid(form) {
        // Ejecutar callback opcional (si lo proporcionaste)
        if (typeof this.config.handleValidForm === 'function') {
            this.config.handleValidForm(form);
        } else if (this.config.dispatchOnSubmit) {
            this.handleValidForm(form);
        } else {
            form.submit();
        }
    }

    /**
     * Método que maneja la acción cuando el formulario es válido.
     * Al ser un método de la clase, no necesitamos usar bind.
     */
    handleValidForm(form) {
        const saveButton = form.querySelector('#save_website_button');
        const allButtons = Array.from(form.querySelectorAll('.btn'));

        this.toggleButtonsState(allButtons, false); // Deshabilitar todos los botones
        this.toggleFormFields(form, false); // Deshabilitar todos los campos del formulario
        this.setButtonLoadingState(saveButton, true); // Poner en estado de carga al botón anfitrión

        // Enviar la solicitud de Livewire correspondiente al enviar el formulario
        const componentEl = form.closest('[wire\\:id]');
        const componentId = componentEl?.getAttribute('wire:id');

        if (componentId) {
            const component = Livewire.find(componentId);
            if (component) {
                component.call(this.config.dispatchOnSubmit);
            } else {
                console.warn('No se encontró el componente Livewire.');
            }
        } else {
            console.warn('No se pudo encontrar wire:id para ejecutar el método Livewire.');
        }
    }

    /**
     * Manejador por defecto para los botones.
     * @param {HTMLElement} form - El formulario actual.
     * @param {HTMLElement} hostButton - El botón anfitrión que disparó el evento.
     * @param {HTMLElement[]} allButtons - Todos los botones relevantes del formulario.
     */
    defaultButtonHandler(form, hostButton, allButtons) {
        this.toggleButtonsState(allButtons, false); // Deshabilitar todos los botones
        this.toggleFormFields(form, false); // Deshabilitar todos los campos del formulario
        this.setButtonLoadingState(hostButton, true); // Poner en estado de carga al botón anfitrión
    }

    /**
     * Deshabilita o habilita los campos del formulario.
     * @param {HTMLElement} form - El formulario actual.
     * @param {boolean} isEnabled - Si los campos deben habilitarse.
     */
    toggleFormFields(form, isEnabled) {
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.disabled = !isEnabled;
        });
    }

    /**
     * Habilita o deshabilita los botones.
     * @param {HTMLElement[]} buttons - Array de botones.
     * @param {boolean} isEnabled - Si los botones deben habilitarse.
     */
    toggleButtonsState(buttons, isEnabled) {
        buttons.forEach(button => {
            if (button){
                button.disabled = !isEnabled;
                button.classList.toggle('disabled', !isEnabled);
            }
        });
    }

    /**
     * Cambia el estado de carga de un botón.
     * @param {HTMLElement} button - Botón que se manejará.
     * @param {boolean} isLoading - Si el botón está en estado de carga.
     */
    setButtonLoadingState(button, isLoading) {
        if (!button) return;

        const loadingText = button.getAttribute('data-loading-text');
        if (loadingText && isLoading) {
            button.setAttribute('data-original-text', button.innerHTML);
            button.innerHTML = loadingText;
            button.disabled = true;
        } else if (!isLoading) {
            button.innerHTML = button.getAttribute('data-original-text') || button.innerHTML;
            button.disabled = false;
        }
    }

    /**
     * Inicializa la validación del formulario con la configuración proporcionada.
     * @param {HTMLElement} form - El formulario que va a ser validado.
     */
    initializeValidation(form) {
        if (this.config.validationConfig) {
            this.formValidationInstance = FormValidation.formValidation(
                form,
                this.config.validationConfig
            ).on('core.form.valid', () => this.handleFormValid(form));

            // Aplicamos el fix después de un pequeño delay
            setTimeout(() => {
                this.fixValidationMessagePosition(form);
            }, 100); // Lo suficiente para esperar a que FV inserte los mensajes
        }
    }

    /**
     * Mueve los mensajes de error fuera del input-group para evitar romper el diseño
     */
    fixValidationMessagePosition(form) {
        const groups = form.querySelectorAll('.input-group.has-validation');

        groups.forEach(group => {
            const errorContainer = group.querySelector('.fv-plugins-message-container');

            if (errorContainer) {
                // Evita duplicados
                if (errorContainer.classList.contains('moved')) return;

                // Crear un contenedor si no existe
                let target = group.parentElement.querySelector('.fv-message');
                if (!target) {
                    target = document.createElement('div');
                    target.className = 'fv-message invalid-feedback';
                    group.parentElement.appendChild(target);
                }

                target.appendChild(errorContainer);
                errorContainer.classList.add('moved'); // Marcar como ya movido
            }
        });
    }

    reloadValidation() {
        const form = document.querySelector(this.config.formSelector);

        if (form && this.formValidationInstance) {
            try {
                setTimeout(() => {
                    this.formValidationInstance.resetForm(); // Limpiar errores
                    this.initializeValidation(form);        // Reinicializar

                    // 🔁 Reconectar eventos de inputs y botones
                    this.initFormEvents(form);
                }, 1);
            } catch (error) {
                console.error('Error al reiniciar la validación:', error);
            }
        } else {
            console.warn('Formulario no encontrado o instancia de validación no disponible.');
        }
    }

}

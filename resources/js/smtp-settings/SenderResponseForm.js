export default class SenderResponseForm {
    constructor(config = {}) {
        const defaultConfig = {
            formSenderResponseId: 'mail-sender-response-settings-card',
            replyToMethodId: 'reply_to_method',
            saveSenderResponseButtonId: 'save_sender_response_button',
            cancelButtonId: 'cancel_sender_response_button'
        };

        this.config = { ...defaultConfig, ...config };
        this.formSenderResponse = null;
        this.smtpReplyToMethod = null;
        this.saveButton = null;
        this.cancelButton = null;

        this.init(); // Inicializa el formulario
    }

    // Método para inicializar el formulario
    init() {
        try {
            // Obtener elementos esenciales
            this.formSenderResponse = document.getElementById(this.config.formSenderResponseId);
            this.smtpReplyToMethod = document.getElementById(this.config.replyToMethodId);
            this.saveButton = document.getElementById(this.config.saveSenderResponseButtonId);
            this.cancelButton = document.getElementById(this.config.cancelButtonId);

            // Asignar eventos
            this.formSenderResponse.addEventListener('input', event => this.handleInput(event));
            this.smtpReplyToMethod.addEventListener('change', () => this.handleToggleReplyToMethod());
            this.cancelButton.addEventListener('click', () => this.handleCancel());

            // Inicializar validación del formulario
            this.initializeFormValidation(this.formSenderResponse, this.senderResponsValidateConfig, () => {
                this.handleFormValid();
            });

            // Disparar el evento 'change' en el método de respuesta
            setTimeout(() => this.smtpReplyToMethod.dispatchEvent(new Event('change')), 0);
        } catch (error) {
            console.error('Error al inicializar el formulario de respuesta:', error);
        }
    }

    /**
     * Método de recarga parcial
     * Este método restablece la validación y los eventos sin destruir la instancia.
     */
    reload() {
        try {
            // Vuelve a inicializar la validación del formulario
            this.initializeFormValidation(this.formSenderResponse, this.senderResponsValidateConfig, () => {
                this.handleFormValid();
            });

            // Vuelve a agregar los eventos (si es necesario, depende de tu lógica)
            this.smtpReplyToMethod.dispatchEvent(new Event('change'));
        } catch (error) {
            console.error('Error al recargar el formulario:', error);
        }
    }

    /**
     * Maneja el evento de entrada en el formulario.
     * @param {Event} event - Evento de entrada.
     */
    handleInput(event) {
        const target = event.target;

        if (['INPUT', 'SELECT', 'TEXTAREA'].includes(target.tagName)) {
            this.toggleButtonsState(this.formSenderResponse, true); // Habilitar botones
        }
    }

    /**
     * Muestra u oculta el campo de correo personalizado según el método de respuesta seleccionado.
     * @param {HTMLElement} smtpReplyToMethod - Elemento select del método de respuesta.
     */
    handleToggleReplyToMethod() {
        const emailCustomDiv = document.querySelector('.email-custom-div');

        if (emailCustomDiv) {
            emailCustomDiv.style.display = Number(this.smtpReplyToMethod.value) === 3 ? 'block' : 'none';
        }
    }

    /**
     * Cancels the sender response form submission.
     * This method disables the form buttons, disables all form fields,
     * and sets the cancel button to a loading state.
     *
     * @returns {void}
     */
    handleCancel() {
        this.disableFormFields(this.formSenderResponse);
        this.toggleButtonsState(this.formSenderResponse, false);
        this.setButtonLoadingState(this.cancelButton, true);
    }

    /**
     * Inicializa la validación del formulario.
     * @param {HTMLElement} form - El formulario.
     * @param {Object} config - Configuración de validación.
     * @param {Function} onValidCallback - Callback cuando el formulario es válido.
     */
    initializeFormValidation(form, config, onValidCallback) {
        return FormValidation.formValidation(form, config).on('core.form.valid', onValidCallback);
    }

    /**
     * Maneja la acción cuando el formulario es válido.
     */
    handleFormValid() {
        this.disableFormFields(this.formSenderResponse);
        this.toggleButtonsState(this.formSenderResponse, false);
        this.setButtonLoadingState(this.saveButton, true);

        Livewire.dispatch('saveMailSenderResponseSettings');
    }

    /**
     * Deshabilita todos los campos del formulario.
     * @param {HTMLElement} form - El formulario.
     */
    disableFormFields(form) {
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.disabled = true;
        });
    }

    /**
     * Habilita o deshabilita los botones dentro del formulario.
     * @param {HTMLElement} form - El formulario.
     * @param {boolean} state - Estado de habilitación.
     */
    toggleButtonsState(form, state) {
        form.querySelectorAll('button').forEach(button => {
            button.disabled = !state;
        });
    }

    /**
     * Configura el estado de carga de un botón.
     * @param {HTMLElement} button - El botón.
     * @param {boolean} isLoading - Si el botón está en estado de carga.
     */
    setButtonLoadingState(button, isLoading) {
        const loadingText = button.getAttribute('data-loading-text');

        if (loadingText && isLoading) {
            button.innerHTML = loadingText;
        } else {
            button.innerHTML = button.getAttribute('data-original-text') || button.innerHTML;
        }
    }

    senderResponsValidateConfig = {
        fields: {
            from_address: {
                validators: {
                    notEmpty: {
                        message: 'El correo electrónico de salida es obligatorio.'
                    },
                    emailAddress: {
                        message: 'Por favor, introduce un correo electrónico válido.'
                    }
                }
            },
            from_name: {
                validators: {
                    notEmpty: {
                        message: 'El nombre del remitente es obligatorio.'
                    }
                }
            },
            reply_to_method: {
                validators: {
                    notEmpty: {
                        message: 'El método de respuesta es obligatorio.'
                    }
                }
            },
            reply_to_email: {
                validators: {
                    callback: {
                        message: 'El correo electrónico de respuesta es obligatorio.',
                        callback: function (input) {
                            if (Number(document.getElementById('reply_to_method').value) === 3) {
                                return input.value.trim() !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value);
                            }
                            return true;
                        }
                    }
                }
            },
            reply_to_name: {
                validators: {
                    callback: {
                        message: 'El nombre de respuesta es obligatorio.',
                        callback: function (input) {
                            if (Number(document.getElementById('reply_to_method').value) === 3) {
                                return input.value.trim() !== '';
                            }
                            return true;
                        }
                    }
                }
            }
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
                eleValidClass: '',
                rowSelector: '.fv-row'
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            autoFocus: new FormValidation.plugins.AutoFocus()
        }
    };
}

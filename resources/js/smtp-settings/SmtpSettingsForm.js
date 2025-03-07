export default class SmtpSettingsForm {
    constructor(config = {}) {
        const defaultConfig = {
            formSmtpSettingsSelector: '#mail-smtp-settings-card',
            changeSmtpSettingsId: 'change_smtp_settings',
            testSmtpConnectionButtonId: 'test_smtp_connection_button',
            saveSmtpConnectionButtonId: 'save_smtp_connection_button',
            cancelSmtpConnectionButtonId: 'cancel_smtp_connection_button'
        };

        this.config = { ...defaultConfig, ...config };
        this.formSmtpSettings = null;
        this.changeSmtpSettingsCheckbox = null;
        this.testButton = null;
        this.saveButton = null;
        this.cancelButton = null;
        this.validator = null;

        this.init();
    }

    /**
     * Inicializa el formulario de configuración SMTP.
     */
    init() {
        try {
            // Obtener elementos esenciales
            this.formSmtpSettings = document.querySelector(this.config.formSmtpSettingsSelector);
            this.notificationArea = this.formSmtpSettings.querySelector(this.config.notificationAreaSelector);
            this.changeSmtpSettingsCheckbox = document.getElementById(this.config.changeSmtpSettingsId);
            this.testButton = document.getElementById(this.config.testSmtpConnectionButtonId);
            this.saveButton = document.getElementById(this.config.saveSmtpConnectionButtonId);
            this.cancelButton = document.getElementById(this.config.cancelSmtpConnectionButtonId);

            // Asignar eventos
            this.changeSmtpSettingsCheckbox.addEventListener('change', event => this.handleCheckboxChange(event));
            this.testButton.addEventListener('click', event => this.handleTestConnection(event));
            this.cancelButton.addEventListener('click', () => this.handleCancel());

            // Inicializar validación del formulario
            this.validator = FormValidation.formValidation(this.formSmtpSettings, this.smtpSettingsFormValidateConfig);
        } catch (error) {
            console.error('Error al inicializar el formulario SMTP:', error);
        }
    }

    /**
     * Método de recarga parcial
     * Este método restablece la validación y los eventos sin destruir la instancia.
     */
    reload() {
        try {
            // Inicializar validación del formulario
            this.validator = FormValidation.formValidation(this.formSmtpSettings, this.smtpSettingsFormValidateConfig);
        } catch (error) {
            console.error('Error al recargar el formulario:', error);
        }
    }

    /**
     * Maneja el cambio en la casilla "Cambiar configuración SMTP".
     * @param {Event} event - Evento de cambio.
     */
    handleCheckboxChange(event) {
        const isEnabled = event.target.checked;

        this.toggleFieldsState(['host', 'port', 'encryption', 'username', 'password'], isEnabled);

        this.testButton.disabled = false;
        this.saveButton.disabled = true;
        this.cancelButton.disabled = false;

        if (!isEnabled) {
            Livewire.dispatch('loadSettings');
        }
    }

    /**
     * Maneja el clic en el botón "Probar conexión".
     * @param {Event} event - Evento de clic.
     */
    handleTestConnection(event) {
        event.preventDefault();

        this.validator.resetForm();

        this.validator.validate().then(status => {
            if (status === 'Valid') {
                this.disableFormFields(this.formSmtpSettings);
                this.toggleButtonsState(this.formSmtpSettings, false);
                this.setButtonLoadingState(this.testButton, true);

                Livewire.dispatch('testSmtpConnection');
            }
        });
    }

    /**
     * Maneja la cancelación de cambios en la configuración SMTP.
     */
    handleCancel() {
        this.disableFormFields(this.formSmtpSettings);
        this.toggleButtonsState(this.formSmtpSettings, false);
        this.setButtonLoadingState(this.cancelButton, true);

        Livewire.dispatch('loadSettings');
    }

    /**
     * Habilita o deshabilita los campos del formulario.
     * @param {Array} fields - IDs de los campos a actualizar.
     * @param {boolean} isEnabled - Estado de habilitación.
     */
    toggleFieldsState(fields, isEnabled) {
        fields.forEach(id => {
            const field = document.getElementById(id);

            if (field) field.disabled = !isEnabled;
        });
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

    smtpSettingsFormValidateConfig = {
        fields: {
            host: {
                validators: {
                    callback: {
                        message: 'El servidor SMTP es obligatorio.',
                        callback: function (input) {
                            // Ejecutar la validación solo si 'change_smtp_settings' está marcado
                            if (document.getElementById('change_smtp_settings').checked) {
                                return input.value.trim() !== '';
                            }
                            return true; // No validar si no está marcado
                        }
                    }
                }
            },
            port: {
                validators: {
                    callback: {
                        message: 'El puerto SMTP es obligatorio.',
                        callback: function (input) {
                            if (document.getElementById('change_smtp_settings').checked) {
                                return (
                                    input.value.trim() !== '' &&
                                    /^\d+$/.test(input.value) &&
                                    input.value >= 1 &&
                                    input.value <= 65535
                                );
                            }
                            return true;
                        }
                    }
                }
            },
            encryption: {
                validators: {
                    callback: {
                        message: 'La encriptación es obligatoria.',
                        callback: function (input) {
                            if (document.getElementById('change_smtp_settings').checked) {
                                return input.value.trim() !== '';
                            }
                            return true;
                        }
                    }
                }
            },
            username: {
                validators: {
                    callback: {
                        message: 'El usuario SMTP es obligatorio.',
                        callback: function (input) {
                            if (document.getElementById('change_smtp_settings').checked) {
                                return input.value.trim().length >= 6;
                            }
                            return true;
                        }
                    }
                }
            },
            password: {
                validators: {
                    callback: {
                        message: 'Por favor, introduzca su contraseña.',
                        callback: function (input) {
                            if (document.getElementById('change_smtp_settings').checked) {
                                return input.value.trim().length >= 5;
                            }
                            return true;
                        }
                    }
                }
            }
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({ rowSelector: '.fv-row' }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            autoFocus: new FormValidation.plugins.AutoFocus()
        }
    };
}

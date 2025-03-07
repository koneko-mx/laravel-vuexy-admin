/**
 * Deshabilita o pone en modo de solo lectura los campos del formulario.
 * @param {string} formSelector - Selector del formulario a deshabilitar.
 */
const disableStoreForm = (formSelector) => {
    const form = document.querySelector(formSelector);
    if (!form) {
        console.warn(`Formulario no encontrado con el selector: ${formSelector}`);
        return;
    }

    /**
     * Habilita o deshabilita un select con Select2.
     * @param {HTMLElement} selectElement - El select afectado.
     * @param {boolean} disabled - Si debe ser deshabilitado.
     */
    const toggleSelect2Disabled = (selectElement, disabled) => {
        selectElement.disabled = disabled;
        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
    };

    /**
     * Aplica modo solo lectura a un select estándar o Select2.
     * @param {HTMLElement} select - El select a modificar.
     * @param {boolean} readonly - Si debe estar en modo solo lectura.
     */
    const setSelectReadonly = (select, readonly) => {
        select.setAttribute('readonly-mode', readonly);
        select.style.pointerEvents = readonly ? 'none' : '';
        select.tabIndex = readonly ? -1 : '';

        if (select.classList.contains('select2-hidden-accessible')) {
            toggleSelect2Disabled(select, readonly);
        }
    };

    /**
     * Aplica modo solo lectura a un checkbox o radio.
     * @param {HTMLElement} checkbox - El input a modificar.
     * @param {boolean} readonly - Si debe ser solo lectura.
     */
    const setCheckboxReadonly = (checkbox, readonly) => {
        checkbox.setAttribute('readonly-mode', readonly);
        checkbox.style.pointerEvents = readonly ? 'none' : '';
        checkbox[readonly ? 'addEventListener' : 'removeEventListener']('click', preventInteraction);
    };

    /**
     * Previene interacción con el elemento.
     * @param {Event} event - El evento de clic.
     */
    const preventInteraction = (event) => event.preventDefault();

    // Obtener todos los inputs del formulario
    const inputs = form.querySelectorAll('input, textarea, select');

    inputs.forEach((el) => {
        if (!el.classList.contains('data-always-enabled')) {
            switch (el.tagName) {
                case 'SELECT':
                    if (el.classList.contains('select2')) {
                        toggleSelect2Disabled(el, true);
                    } else {
                        setSelectReadonly(el, true);
                    }
                    break;
                case 'INPUT':
                    if (['checkbox', 'radio'].includes(el.type)) {
                        setCheckboxReadonly(el, true);
                    } else {
                        el.readOnly = true;
                    }
                    break;
                case 'TEXTAREA':
                    el.readOnly = true;
                    break;
            }
        }
    });
};

// Hacer la función accesible globalmente
window.disableStoreForm = disableStoreForm;

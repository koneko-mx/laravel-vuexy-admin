import '../../assets/js/notifications/LivewireNotification.js';
import FormCustomListener from '../../assets/js/forms/formCustomListener';

new FormCustomListener({
    buttonSelectors: ['.btn-save', '.btn-cancel', '.btn-reset'] // Selectores para botones
});

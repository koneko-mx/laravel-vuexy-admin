import '../../assets/js/notifications/LivewireNotification.js';
import FormCustomListener from '../../assets/js/forms/formCustomListener';

new FormCustomListener({
    buttonSelectors: ['.btn-save', '.btn-cancel', '.btn-reset'] // Selectores para botones
});

Livewire.on('clearLocalStoregeTemplateCustomizer', event => {
    const _deleteCookie = name => {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    };

    const pattern = 'templateCustomizer-';

    // Iterar sobre todas las claves en localStorage
    Object.keys(localStorage).forEach(key => {
        if (key.startsWith(pattern)) {
            localStorage.removeItem(key);
        }
    });

    _deleteCookie('admin-mode');
    _deleteCookie('admin-colorPref');
    _deleteCookie('colorPref');
    _deleteCookie('theme');
    _deleteCookie('direction');
});

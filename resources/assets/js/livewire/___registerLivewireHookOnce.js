/**
 * Registra un hook Livewire solo una vez por componente.
 * @param {string} hookName - Nombre del hook Livewire (por ejemplo, "morphed").
 * @param {string} componentName - Nombre exacto del componente Livewire.
 * @param {function} callback - Función que se ejecutará una vez por hook+componente.
 */
export default function registerLivewireHookOnce(hookName, componentName, callback) {
    if (!hookName || !componentName || typeof callback !== 'function') {
        console.warn('[registerLivewireHookOnce] Parámetros inválidos.');
        return;
    }

    // Clave única para este hook+componente
    const safeName = componentName.replace(/[^a-zA-Z0-9]/g, '_');
    const key = `__hook_${hookName}_${safeName}`;

    if (!window[key]) {
        window[key] = true;

        Livewire.hook(hookName, ({ component }) => {
            if (component.name === componentName) {
                // console.info(`[Livewire Hook Triggered] ${hookName} for ${component.name}`);
                callback(component);
            }
        });
    }
}

if(!window.registerLivewireHookOnce) {
    window.registerLivewireHookOnce = registerLivewireHookOnce;
}

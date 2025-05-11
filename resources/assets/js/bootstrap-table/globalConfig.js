const appRoutesElement = document.getElementById('app-routes');

export const routes = appRoutesElement ? JSON.parse(appRoutesElement.textContent) : {};

export const booleanStatusCatalog = {
    activo: {
        trueText: 'Activo',
        falseText: 'Inactivo',
        trueClass: 'badge bg-label-success',
        falseClass: 'badge bg-label-danger',
    },
    habilitado: {
        trueText: 'Habilitado',
        falseText: 'Deshabilitado',
        trueClass: 'badge bg-label-success',
        falseClass: 'badge bg-label-danger',
        trueIcon: 'ti ti-checkup-list',
        falseIcon: 'ti ti-ban',
    },
    checkSI: {
        trueText: 'SI',
        falseIcon: '',
        trueClass: 'badge bg-label-info',
        falseText: '',
    },
    check: {
        trueIcon: 'ti ti-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    simpleCheck: {
        trueText: '',
        trueIcon: 'ti ti-check',
        falseIcon: '',
        trueClass: 'text-green-900',
        falseText: '',
    },
    checkbox: {
        trueIcon: 'ti ti-checkbox',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    checklist: {
        trueIcon: 'ti ti-checklist',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    phone_done: {
        trueIcon: 'ti ti-phone-done',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    checkup_list: {
        trueIcon: 'ti ti-checkup-list',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    list_check: {
        trueIcon: 'ti ti-list-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    camera_check: {
        trueIcon: 'ti ti-camera-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    mail_check: {
        trueIcon: 'ti ti-mail-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    clock_check: {
        trueIcon: 'ti ti-clock-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    user_check: {
        trueIcon: 'ti ti-user-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    circle_check: {
        trueIcon: 'ti ti-circle-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    shield_check: {
        trueIcon: 'ti ti-shield-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    },
    calendar_check: {
        trueIcon: 'ti ti-calendar-check',
        falseIcon: '',
        trueClass: 'text-green-800',
        falseClass: '',
    }
};

export const badgeColorCatalog = {
    primary: { color: 'primary' },
    secondary: { color: 'secondary' },
    success: { color: 'success' },
    danger: { color: 'danger' },
    warning: { color: 'warning' },
    info: { color: 'info' },
    dark: { color: 'dark' },
    light: { color: 'light', textColor: 'text-dark' }
};

export const statusIntBadgeBgCatalogCss = {
    1: 'warning',
    2: 'info',
    10: 'success',
    12: 'danger',
    11: 'warning'
};

export const statusIntBadgeBgCatalog = {
    1: 'Inactivo',
    2: 'En proceso',
    10: 'Activo',
    11: 'Archivado',
    12: 'Cancelado',
};

/**
 * Genera la URL del avatar basado en iniciales o devuelve la foto de perfil si está disponible.
 * @param {string} fullName - Nombre completo del usuario.
 * @param {string|null} profilePhoto - Ruta de la foto de perfil.
 * @returns {string} - URL del avatar.
 */
export const getAvatarUrl = (fullName, profilePhoto) => {
    const baseUrl = window.baseUrl || '';

    if (profilePhoto) {
        return `${baseUrl}storage/profile-photos/${profilePhoto}`;
    }

    const name = fullName.replace(/\s+/g, '-').toLowerCase();

    return `${baseUrl}admin/usuario/avatar/?name=${fullName}`;
}

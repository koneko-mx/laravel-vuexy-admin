import { routes, getAvatarUrl, booleanStatusCatalog, statusIntBadgeBgCatalogCss, statusIntBadgeBgCatalog } from '@vuexy-admin/assets/js/bootstrap-table/globalConfig';

export const userActionFormatter = (value, row, index) => {
    if (!row.id) return '';

    const showUrl = routes['admin.user.show'].replace(':id', row.id);
    const editUrl = routes['admin.user.edit'].replace(':id', row.id);
    const deleteUrl = routes['admin.user.delete'].replace(':id', row.id);

    return `
        <div class="flex justify-center space-x-2">
            <a href="${editUrl}" title="Editar" class="icon-button hover:text-slate-700">
                <i class="ti ti-edit"></i>
            </a>
            <a href="${deleteUrl}" title="Eliminar" class="icon-button hover:text-slate-700">
                <i class="ti ti-trash"></i>
            </a>
            <a href="${showUrl}" title="Ver" class="icon-button hover:text-slate-700">
                <i class="ti ti-eye"></i>
            </a>
        </div>
    `.trim();
};


export const userLoginActionFormatter = (value, row, index) => {
    if (!row.user_id) return '';

    //const showUrl = routes['admin.user-login.show'].replace(':id', row.user_id);
    const showUrl = '#';

    return `
        <div class="flex justify-center space-x-2">
            <a href="${showUrl}" title="Ver" class="icon-button hover:text-slate-700">
                <i class="ti ti-eye"></i>
            </a>
        </div>
    `.trim();
};




export const permissionsActionFormatter = (value, row, index) => {
    if (!row.id) return '';

    return `
        <div class="flex justify-center space-x-2">
            <a href="javascript:;" title="Editar" class="mx-2" wire:click="dispatch('editPermission', {id:${row.id}})">
                <i class="ti ti-edit"></i>
            </a>
            <a href="javascript:;" title="Eliminar" class="mx-2" wire:click="dispatch('confirmDeletionPermission', {id:${row.id}})">
                <i class="ti ti-trash"></i>
            </a>
        </div>
    `.trim();
};




export const dynamicBooleanFormatter = (value, row, index, options = {}) => {
    const { tag = 'default', customOptions = {} } = options;
    const catalogConfig = booleanStatusCatalog[tag] || {};

    const finalOptions = {
        ...catalogConfig,
        ...customOptions, // Permite sobreescribir la configuración predeterminada
        ...options // Permite pasar opciones rápidas
    };

    const {
        trueIcon = '',
        falseIcon = '',
        trueText = 'Sí',
        falseText = 'No',
        trueClass = 'badge bg-label-success',
        falseClass = 'badge bg-label-danger',
        iconClass = 'text-green-800'
    } = finalOptions;

    const trueElement  = !trueIcon && !trueText ? '' : `<span class="${trueClass}">${trueIcon ? `<i class="${trueIcon} ${iconClass}"></i> ` : ''}${trueText}</span>`;
    const falseElement = !falseIcon && !falseText ? '' : `<span class="${falseClass}">${falseIcon ? `<i class="${falseIcon}"></i> ` : ''}${falseText}</span>`;

    return value? trueElement : falseElement;
};

export const dynamicBadgeFormatter = (value, row, index, options = {}) => {
    if (!value) return '';

    const {
        color = 'primary', // Valor por defecto
        textColor = '',    // Permite agregar color de texto si es necesario
        additionalClass = '' // Permite añadir clases adicionales
    } = options;

    return `<span class="badge bg-${color} ${textColor} ${additionalClass}">${value}</span>`;
};

export const jsonLanguageFormatter = (value, row, index) => {
    const language = document.documentElement.lang;

    try {
        const json = JSON.parse(value);

        return json[language] || json['es'] || json['en'] || value;

    } catch (e) {
        return '';
    }
}


export const statusIntBadgeBgFormatter = (value, row, index) => {
    return value
        ? `<span class="badge bg-label-${statusIntBadgeBgCatalogCss[value]}">${statusIntBadgeBgCatalog[value]}</span>`
        : '';
}

export const textNowrapFormatter = (value, row, index) => {
    if (!value) return '';
        return `<span class="text-nowrap">${value}</span>`;
}



export const textXsFormatter = (value, row, index) => {
    if (!value) return '';
    return `<span class="text-xs">${value}</span>`;
};





export const dateClassicFormatter = (value) => {
    if (!value) return '';

    const date = new Date(value);

    if (isNaN(date)) return value;

    const fecha = date.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const hora  = value.length == 19? date.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' }): false;

    let fechaHora = hora
        ? `<span class="inline-flex items-center gap-1 text-blue-700 text-xs px-2 py-0.5 me-1">
            <i class="ti ti-calendar-event text-[14px]"></i> ${fecha}
        </span>`
        : `<span class="inline-flex items-center gap-1 text-blue-700 px-2 py-1">
            <i class="ti ti-calendar-event text-[16px]"></i> ${fecha}
        </span>`;

    if (hora) {
        fechaHora += `
            <span class="inline-flex items-center gap-1 text-gray-700 text-xs px-2 py-0.5">
                <i class="ti ti-clock text-[14px]"></i> ${hora}
            </span>
        `.trim();
    }

    return fechaHora.trim();
};

export const dateLimeFormatter = (value) => {
    if (!value) return '';

    const date = new Date(value);

    if (isNaN(date)) return value;

    const fecha = date.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const hora  = value.length == 19? date.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' }): false;

    let fechaHora = hora
        ? `<span class="inline-flex items-center gap-1 text-lime-800 text-xs px-2 py-0.5 me-1">
            <i class="ti ti-calendar-event text-[14px]"></i> ${fecha}
        </span>`
        : `<span class="inline-flex items-center gap-1 text-lime-800 px-2 py-1">
            <i class="ti ti-calendar-event text-[16px]"></i> ${fecha}
        </span>`;

    if (hora) {
        fechaHora += `
            <span class="inline-flex items-center gap-1 text-lime-600 text-xs px-2 py-0.5">
                <i class="ti ti-clock text-[14px]"></i> ${hora}
            </span>
        `.trim();
    }

    return fechaHora.trim();
};

export const dateBadgeBlueFormatter = (value) => {
    if (!value) return '';

    const date = new Date(value);

    if (isNaN(date)) return value;

    const fecha = date.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const hora  = value.length == 19? date.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' }): false;

    let fechaHora = hora
        ? `<span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 text-xs px-2 py-0.5 me-1">
            <i class="ti ti-calendar-event text-[14px]"></i> ${fecha}
        </span>`
        : `<span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 px-2 py-1">
            <i class="ti ti-calendar-event text-[16px]"></i> ${fecha}
        </span>`;

    if (hora) {
        fechaHora += `
            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 text-gray-700 text-xs px-2 py-0.5">
                <i class="ti ti-clock text-[14px]"></i> ${hora}
            </span>
        `.trim();
    }

    return fechaHora.trim();
};

export const dateBadgeAmberFormatter = (value) => {
    if (!value) return '';

    const date = new Date(value);

    if (isNaN(date)) return value;

    const fecha = date.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const hora  = value.length == 19? date.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' }): false;

    let fechaHora = hora
        ? `<span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-700 text-xs px-2 py-0.5 me-1">
            <i class="ti ti-calendar-event text-[14px]"></i> ${fecha}
        </span>`
        : `<span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-700 px-2 py-1">
            <i class="ti ti-calendar-event text-[16px]"></i> ${fecha}
        </span>`;

    if (hora) {
        fechaHora += `
            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-600 text-xs px-2 py-0.5">
                <i class="ti ti-clock text-[14px]"></i> ${hora}
            </span>
        `.trim();
    }

    return fechaHora.trim();
};




export const emailFormatter = (value, row, index) => {
    if (!value) return '';

    return `
        <a href="mailto:${value}" class="flex items-center space-x-2 text-blue-600">
            <i class="ti ti-mail-filled"></i>
            <span class="whitespace-nowrap hover:underline">${value}</span>
        </a>
    `.trim();
};






/**
 * Formatter para mostrar duración de sesión del usuario.
 *
 * @param {string} row.created_at - Fecha de inicio de sesión.
 * @param {string|null} row.logout_at - Fecha de cierre de sesión o null si aún activa.
 * @returns {string} Duración de la sesión formateada o estado de sesión activa.
 */
export const sessionDurationFormatter = (value, row, index) => {
    if (!row.created_at) return '<span class="text-muted">-</span>';

    const start = new Date(row.created_at);
    const end = row.logout_at ? new Date(row.logout_at) : new Date();
    const diffMs = end - start;

    if (diffMs < 0) return '<span class="text-danger">Invalido</span>';

    const diffSeconds = Math.floor(diffMs / 1000);
    const hours = Math.floor(diffSeconds / 3600);
    const minutes = Math.floor((diffSeconds % 3600) / 60);
    const seconds = diffSeconds % 60;

    const formattedTime = [
        hours ? `${hours}h` : '',
        minutes ? `${minutes}m` : '',
        `${seconds}s`
    ].filter(Boolean).join(' ');

    return row.logout_at
        ? `<span class="text-xs font-medium text-slate-700">${formattedTime}</span>`
        : `<span class="text-xs font-semibold text-success-600">🟢 Sesión activa (${formattedTime})</span>`;
};











/**
 * @param {int} row.id - Identificador del usuario
 * @param {string} row.full_name - Nombre completo del usuario
 * @param {string} row.profile_photo_path - Ruta de la foto de perfil
 * @param {string} row.email - Correo electrónico del usuario
 * @returns {string} HTML con el perfil del usuario
 */
export const userIdProfileFormatter = (value, row, index) => {
    if (!row.id) return '';

    const avatar = getAvatarUrl(row.full_name, row.profile_photo_path);
    const email  = row.email ? row.email : 'Sin correo';

    const profileUrl = routes['admin.user.show'].replace(':id', row.id);

    return formatterProfileElement(row.id, row.full_name, avatar, email, profileUrl);
};

const formatterProfileElement = (id, name, avatar, email, profileUrl) => {
    return `
        <div class="flex items-center space-x-3" style="min-width: 240px">
            <a href="${profileUrl}" class="flex-shrink-0">
                <img src="${avatar}" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-300 shadow-sm hover:scale-105 transition-transform">
            </a>
            <div class="truncate">
                <a href="${profileUrl}" class="font-medium text-slate-700 hover:underline block text-wrap">${name}</a>
                <small class="text-muted block truncate">${email}</small>
            </div>
        </div>
    `.trim();
};



/**
 * @param {int} row.user_id - Identificador del usuario
 * @param {string} row.user_name - Nombre del usuario
 * @param {string} row.user_profile_photo_path - Ruta de la foto de perfil
 * @param {string} row.user_email - Correo electrónico del usuario
 * @returns {string} HTML con el perfil del usuario
 */
export const userProfileFormatter = (value, row, index) => {
    if (!row.user_id) return '';

    const profileUrl = routes['admin.user.show'].replace(':id', row.user_id);
    const avatar     = getAvatarUrl(row.user_name, row.user_profile_photo_path);
    const email      = row.user_email ? row.user_email : 'Sin correo';

    return `
        <div class="flex items-center space-x-3" style="min-width: 240px">
            <a href="${profileUrl}" class="flex-shrink-0">
                <img src="${avatar}" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-300 shadow-sm hover:scale-105 transition-transform">
            </a>
            <div class="truncate">
                <a href="${profileUrl}" class="font-medium text-slate-700 hover:underline block text-wrap">${row.user_name}</a>
                <small class="text-muted block truncate">${email}</small>
            </div>
        </div>
    `.trim();
};



export const creatorProfileFormatter = (value, row, index) => {
    if (!row.created_by) return '';

    const profileUrl = routes['admin.user.show'].replace(':id', row.created_by);
    const avatar = getAvatarUrl(row.creator_name, row.creator_profile_photo_path);
    const email = row.creator_email ? row.creator_email : 'Sin correo';

    return `
        <div class="flex items-center space-x-3" style="min-width: 240px">
            <a href="${profileUrl}" class="flex-shrink-0">
                <img src="${avatar}" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-300 shadow-sm hover:scale-105 transition-transform">
            </a>
            <div class="truncate">
                <a href="${profileUrl}" class="font-medium text-slate-700 hover:underline block text-wrap">${row.creator_name}</a>
                <small class="text-muted block truncate">${email}</small>
            </div>
        </div>
    `.trim();
};


export const updaterProfileFormatter = (value, row, index) => {
    if (!row.updated_by) return '';

    const profileUrl = routes['admin.user.show'].replace(':id', row.updated_by);
    const avatar = getAvatarUrl(row.updater_name, row.updater_profile_photo_path);
    const email = row.updater_email ? row.updater_email : 'Sin correo';

    return `
        <div class="flex items-center space-x-3" style="min-width: 240px">
            <a href="${profileUrl}" class="flex-shrink-0">
                <img src="${avatar}" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-300 shadow-sm hover:scale-105 transition-transform">
            </a>
            <div class="truncate">
                <a href="${profileUrl}" class="font-medium text-slate-700 hover:underline block text-wrap">${row.updater_name}</a>
                <small class="text-muted block truncate">${email}</small>
            </div>
        </div>
    `.trim();
};


/**
 * Convierte bytes en un formato legible por humanos.
 *
 * @param {int} value - Valor en bytes
 * @returns {string} Tamaño en formato legible
 */
export const bytesToHumanReadable = (value, row, index) => {
    if (!value || value === 0) return;

    const precision = 2; // define aquí la precisión que desees
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    const base = Math.floor(Math.log(value) / Math.log(1024));

    return (value / Math.pow(1024, base)).toFixed(precision) + ' ' + units[base];
};


export const userRoleFormatter = (value, row, index) => {
    if (!value) return '';

    const rolesStyles = window.userRoleStyles || {};

    return value.split('|').map(role => {
        const trimmedRole = role.trim();
        const color = rolesStyles[trimmedRole] || 'secondary'; // fallback si no existe

        return `<span class="badge bg-label-${color} mr-2 mb-2">${trimmedRole}</span>`;
    }).join('');
};







export const booleanStatusFormatter = (value, row, index) => {
    return `<span class="badge bg-label-${value ? 'success' : 'danger'}">${value ? 'Activo' : 'Inactivo'}</span>`;
};









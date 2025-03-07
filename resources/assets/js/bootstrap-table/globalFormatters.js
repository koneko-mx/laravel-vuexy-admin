import { booleanStatusCatalog, statusIntBadgeBgCatalogCss, statusIntBadgeBgCatalog } from './globalConfig';
import {routes} from '../../../../../laravel-vuexy-admin/resources/assets/js/bootstrap-table/globalConfig.js';

export const userActionFormatter = (value, row, index) => {
    if (!row.id) return '';

    const showUrl = routes['admin.user.show'].replace(':id', row.id);
    const editUrl = routes['admin.user.edit'].replace(':id', row.id);
    const deleteUrl = routes['admin.user.delete'].replace(':id', row.id);

    return `
        <div class="flex space-x-2">
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
    const {
        color = 'primary', // Valor por defecto
        textColor = '',    // Permite agregar color de texto si es necesario
        additionalClass = '' // Permite añadir clases adicionales
    } = options;

    return `<span class="badge bg-${color} ${textColor} ${additionalClass}">${value}</span>`;
};

export const statusIntBadgeBgFormatter = (value, row, index) => {
    return value
        ? `<span class="badge bg-label-${statusIntBadgeBgCatalogCss[value]}">${statusIntBadgeBgCatalog[value]}</span>`
        : '';
}

export const textNowrapFormatter = (value, row, index) => {
    if (!value) return '';
        return `<span class="text-nowrap">${value}</span>`;
}


export const toCurrencyFormatter = (value, row, index) => {
    return isNaN(value) ? '' : Number(value).toCurrency();
}

export const numberFormatter = (value, row, index) => {
    return isNaN(value) ? '' : Number(value);
}

export const monthFormatter = (value, row, index) => {
    switch (parseInt(value)) {
        case 1:
            return 'Enero';
        case 2:
            return 'Febrero';
        case 3:
            return 'Marzo';
        case 4:
            return 'Abril';
        case 5:
            return 'Mayo';
        case 6:
            return 'Junio';
        case 7:
            return 'Julio';
        case 8:
            return 'Agosto';
        case 9:
            return 'Septiembre';
        case 10:
            return 'Octubre';
        case 11:
            return 'Noviembre';
        case 12:
            return 'Diciembre';
    }
}

export const humaneTimeFormatter = (value, row, index) => {
    return isNaN(value) ? '' : Number(value).humaneTime();
}

/**
 * Genera la URL del avatar basado en iniciales o devuelve la foto de perfil si está disponible.
 * @param {string} fullName - Nombre completo del usuario.
 * @param {string|null} profilePhoto - Ruta de la foto de perfil.
 * @returns {string} - URL del avatar.
 */
function getAvatarUrl(fullName, profilePhoto) {
    const baseUrl = window.baseUrl || '';

    if (profilePhoto) {
        return `${baseUrl}storage/profile-photos/${profilePhoto}`;
    }

    return `${baseUrl}admin/usuario/avatar/?name=${fullName}`;
}

/**
 * Formatea la columna del perfil de usuario con avatar, nombre y correo.
 */
export const userProfileFormatter = (value, row, index) => {
    if (!row.id) return '';

    const profileUrl = routes['admin.user.show'].replace(':id', row.id);
    const avatar = getAvatarUrl(row.full_name, row.profile_photo_path);
    const email = row.email ? row.email : 'Sin correo';

    return `
        <div class="flex items-center space-x-3" style="min-width: 240px">
            <a href="${profileUrl}" class="flex-shrink-0">
                <img src="${avatar}" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-300 shadow-sm hover:scale-105 transition-transform">
            </a>
            <div class="truncate">
                <a href="${profileUrl}" class="font-medium text-slate-700 hover:underline block text-wrap">${row.full_name}</a>
                <small class="text-muted block truncate">${email}</small>
            </div>
        </div>
    `;
};

/**
 * Formatea la columna del perfil de contacto con avatar, nombre y correo.
 */
export const contactProfileFormatter = (value, row, index) => {
    if (!row.id) return '';

    const profileUrl = routes['admin.contact.show'].replace(':id', row.id);
    const avatar = getAvatarUrl(row.full_name, row.profile_photo_path);
    const email = row.email ? row.email : 'Sin correo';

    return `
        <div class="flex items-center space-x-3" style="min-width: 240px">
            <a href="${profileUrl}" class="flex-shrink-0">
                <img src="${avatar}" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-300 shadow-sm hover:scale-105 transition-transform">
            </a>
            <div class="truncate">
                <a href="${profileUrl}" class="font-medium text-slate-700 hover:underline block text-wrap">${row.full_name}</a>
                <small class="text-muted block truncate">${email}</small>
            </div>
        </div>
    `;
};



export const creatorFormatter = (value, row, index) => {
    if (!row.creator) return '';

    const email   = row.creator_email || 'Sin correo';
    const showUrl = routes['admin.user.show'].replace(':id', row.id);


    return `
        <div class="flex flex-col">
            <a href="${showUrl}" class="font-medium text-slate-600 hover:underline block text-wrap">${row.creator}</a>
            <small class="text-muted">${email}</small>
        </div>
    `;
};


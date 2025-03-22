import '../../vendor/libs/bootstrap-table/bootstrap-table';
import '../notifications/LivewireNotification';

class BootstrapTableManager {
    constructor(bootstrapTableWrap, config = {}) {
        const defaultConfig = {
            header: [],
            format: [],
            search_columns: [],
            actionColumn: false,
            height: 'auto',
            minHeight: 300,
            bottomMargin : 195,
            search: true,
            showColumns: true,
            showColumnsToggleAll: true,
            showExport: true,
            exportfileName: 'datatTable',
            exportWithDatetime: true,
            showFullscreen: true,
            showPaginationSwitch: true,
            showRefresh: true,
            showToggle: true,
            /*
            smartDisplay: false,
            searchOnEnterKey: true,
            showHeader: false,
            showFooter: true,
            showRefresh: true,
            showToggle: true,
            showFullscreen: true,
            detailView: true,
            searchAlign: 'right',
            buttonsAlign: 'right',
            toolbarAlign: 'left',
            paginationVAlign: 'bottom',
            paginationHAlign: 'right',
            paginationDetailHAlign:	'left',
            paginationSuccessivelySize: 5,
            paginationPagesBySide: 3,
            paginationUseIntermediate: true,
            */
            clickToSelect: true,
            minimumCountColumns: 4,
            fixedColumns: true,
            fixedNumber: 1,
            idField: 'id',
            pagination: true,
            pageList: [25, 50, 100, 500, 1000],
            sortName: 'id',
            sortOrder: 'asc',
            cookie: false,
            cookieExpire: '365d',
            cookieIdTable: 'myTableCookies', // Nombre único para las cookies de la tabla
            cookieStorage: 'localStorage',
            cookiePath: '/',
        };

        this.$bootstrapTable  = $('.bootstrap-table', bootstrapTableWrap);
        this.$toolbar         = $('.bt-toolbar', bootstrapTableWrap);
        this.$searchColumns   = $('.search_columns', bootstrapTableWrap);
        this.$btnRefresh      = $('.btn-refresh', bootstrapTableWrap);
        this.$btnClearFilters = $('.btn-clear-filters', bootstrapTableWrap);

        this.config = { ...defaultConfig, ...config };

        this.config.toolbar       = `${bootstrapTableWrap} .bt-toolbar`;
        this.config.height        = this.config.height == 'auto'? this.getTableHeight(): this.config.height;
        this.config.cookieIdTable = this.config.exportWithDatetime? this.config.cookieIdTable + '-' + this.getFormattedDateYMDHm(): this.config.cookieIdTable;

        this.tableFormatters = {}; // Mueve la carga de formatters aquí

        this.initTable();
    }

    /**
     * Calcula la altura de la tabla.
     */
    getTableHeight() {
        const btHeight = window.innerHeight - this.$toolbar.height() - this.bottomMargin;

        return btHeight < this.config.minHeight ? this.config.minHeight : btHeight;
    }

    /**
     * Genera un ID único para la tabla basado en una cookie.
     */
    getCookieId() {
        const generateShortHash = (str) => {
            let hash = 0;

            for (let i = 0; i < str.length; i++) {
                const char = str.charCodeAt(i);

                hash = (hash << 5) - hash + char;
                hash &= hash; // Convertir a entero de 32 bits
            }

            return Math.abs(hash).toString().substring(0, 12);
        };

        return `bootstrap-table-cache-${generateShortHash(this.config.title)}`;
    }

    /**
     * Carga los formatters dinámicamente
     */
    async loadFormatters() {
        //const formattersModules = import.meta.glob('../../../../../**/resources/assets/js/bootstrap-table/*Formatters.js');
        const formattersModules = import.meta.glob('/vendor/koneko/laravel-vuexy-admin/resources/assets/js/bootstrap-table/*Formatters.js');

        const formatterPromises = Object.entries(formattersModules).map(async ([path, importer]) => {
            const module = await importer();
            Object.assign(this.tableFormatters, module);
        });

        await Promise.all(formatterPromises);
    }

    btColumns() {
        const columns = [];

        Object.entries(this.config.header).forEach(([key, value]) => {
            const columnFormat = this.config.format[key] || {};

            if (typeof columnFormat.formatter === 'object') {
                const formatterName = columnFormat.formatter.name;
                const formatterParams = columnFormat.formatter.params || {};

                const formatterFunction = this.tableFormatters[formatterName];
                if (formatterFunction) {
                    columnFormat.formatter = (value, row, index) => formatterFunction(value, row, index, formatterParams);
                } else {
                    console.warn(`Formatter "${formatterName}" no encontrado para la columna "${key}"`);
                }
            } else if (typeof columnFormat.formatter === 'string') {
                const formatterFunction = this.tableFormatters[columnFormat.formatter];
                if (formatterFunction) {
                    columnFormat.formatter = formatterFunction;
                }
            }

            if (columnFormat.onlyFormatter) {
                columns.push({
                    align: 'center',
                    formatter: columnFormat.formatter || (() => ''),
                    forceHide: true,
                    switchable: false,
                    field: key,
                    title: value,
                });
                return;
            }

            const column = {
                title: value,
                field: key,
                sortable: true,
            };

            columns.push({ ...column, ...columnFormat });
        });

        return columns;
    }



    /**
     * Petición AJAX para la tabla.
     */
    ajaxRequest(params) {
        const url = `${window.location.href}?${$.param(params.data)}&${$('.bt-toolbar :input').serialize()}`;

        $.get(url).then((res) => params.success(res));
    }

    toValidFilename(str, extension = 'txt') {
        return str
            .normalize("NFD") // 🔹 Normaliza caracteres con tilde
            .replace(/[\u0300-\u036f]/g, "") // 🔹 Elimina acentos y diacríticos
            .replace(/[<>:"\/\\|?*\x00-\x1F]/g, '') // 🔹 Elimina caracteres inválidos
            .replace(/\s+/g, '-') // 🔹 Reemplaza espacios con guiones
            .replace(/-+/g, '-') // 🔹 Evita múltiples guiones seguidos
            .replace(/^-+|-+$/g, '') // 🔹 Elimina guiones al inicio y fin
            .toLowerCase() // 🔹 Convierte a minúsculas
            + (extension ? '.' + extension.replace(/^\.+/, '') : ''); // 🔹 Asegura la extensión válida
    }

    getFormattedDateYMDHm(date = new Date()) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // 🔹 Asegura dos dígitos
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${year}${month}${day}-${hours}${minutes}`;
    }


    /**
     * Inicia la tabla después de cargar los formatters
     */
    async initTable() {
        await this.loadFormatters(); // Asegura que los formatters estén listos antes de inicializar

        this.$bootstrapTable
            .bootstrapTable('destroy').bootstrapTable({
                height: this.config.height,
                locale: 'es-MX',
                ajax: (params) => this.ajaxRequest(params),
                toolbar: this.config.toolbar,
                search: this.config.search,
                showColumns: this.config.showColumns,
                showColumnsToggleAll: this.config.showColumnsToggleAll,
                showExport: this.config.showExport,
                exportTypes: ['csv', 'txt', 'xlsx'],
                exportOptions: {
                    fileName: this.config.fileName,
                },
                showFullscreen: this.config.showFullscreen,
                showPaginationSwitch: this.config.showPaginationSwitch,
                showRefresh: this.config.showRefresh,
                showToggle: this.config.showToggle,
                clickToSelect: this.config.clickToSelect,
                minimumCountColumns: this.config.minimumCountColumns,
                fixedColumns: this.config.fixedColumns,
                fixedNumber: this.config.fixedNumber,
                idField: this.config.idField,
                pagination: this.config.pagination,
                pageList: this.config.pageList,
                sidePagination: "server",
                sortName: this.config.sortName,
                sortOrder: this.config.sortOrder,
                mobileResponsive: true,
                resizable: true,
                cookie: this.config.cookie,
                cookieExpire: this.config.cookieExpire,
                cookieIdTable: this.config.cookieIdTable,
                columns: this.btColumns(),
            });
    }

}

window.BootstrapTableManager = BootstrapTableManager;

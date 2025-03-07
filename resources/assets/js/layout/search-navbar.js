/**
 * Search Navbar
 */

'use strict';

$(function () {
    window.Helpers.initSidebarToggle();
    // Toggle Universal Sidebar

    // Navbar Search with autosuggest (typeahead)
    var searchToggler = $('.search-toggler'),
        searchInputWrapper = $('.search-input-wrapper'),
        searchInput = $('.search-input'),
        contentBackdrop = $('.content-backdrop');

    // Open search input on click of search icon
    if (searchToggler.length) {
        searchToggler.on('click', function () {
            if (searchInputWrapper.length) {
                searchInputWrapper.toggleClass('d-none');
                searchInput.trigger('focus');
            }
        });

        document.addEventListener('keydown', function (event) {
            const ctrlKey = event.ctrlKey;
            const slashKey = event.key === '/'; // Usa 'key' para obtener la tecla como texto

            if (ctrlKey && slashKey) {
                const searchInputWrapper = document.querySelector('.search-input-wrapper');
                const searchInput = document.querySelector('.search-input');

                if (searchInputWrapper) {
                    searchInputWrapper.classList.toggle('d-none'); // Alterna la visibilidad
                    if (searchInput) {
                        searchInput.focus(); // Coloca el foco en el input
                    }
                }
            }
        });

        // Note: Following code is required to update container class of typeahead dropdown width on focus of search input. setTimeout is required to allow time to initiate Typeahead UI.
        setTimeout(function () {
            var twitterTypeahead = $('.twitter-typeahead');

            searchInput.on('focus', function () {
                if (searchInputWrapper.hasClass('container-xxl')) {
                    searchInputWrapper.find(twitterTypeahead).addClass('container-xxl');
                    twitterTypeahead.removeClass('container-fluid');
                } else if (searchInputWrapper.hasClass('container-fluid')) {
                    searchInputWrapper.find(twitterTypeahead).addClass('container-fluid');
                    twitterTypeahead.removeClass('container-xxl');
                }
            });
        }, 10);
    }

    if (searchInput.length) {
        // Función para normalizar cadenas (eliminar acentos)
        function normalizeString(str) {
            return str
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase();
        }

        // Filter config con soporte para ignorar acentos
        var filterConfig = function (data) {
            return function findMatches(q, cb) {
                let matches = [];

                // Normalizar la consulta
                const normalizedQuery = normalizeString(q);

                data.filter(function (i) {
                    const normalizedName = normalizeString(i.name);

                    if (normalizedName.startsWith(normalizedQuery)) {
                        matches.push(i);
                    } else if (
                        !normalizedName.startsWith(normalizedQuery) &&
                        normalizedName.includes(normalizedQuery)
                    ) {
                        matches.push(i);

                        // Ordenar por coincidencia secundaria
                        matches.sort(function (a, b) {
                            return b.name < a.name ? 1 : -1;
                        });
                    }
                });

                cb(matches);
            };
        };

        // Search JSON
        var searchJson = 'search-navbar'; // For vertical layout

        if ($('#layout-menu').hasClass('menu-horizontal')) {
            var searchJson = 'search-navbar'; // For vertical layout
        }

        // Search API AJAX call
        var searchData = $.ajax({
            url: assetsPath + '../../admin/' + searchJson, //? Use your own search api instead
            dataType: 'json',
            async: false
        }).responseJSON;

        // Init typeahead on searchInput
        searchInput.each(function () {
            var $this = $(this);

            searchInput
                .typeahead(
                    {
                        hint: false,
                        classNames: {
                            menu: 'tt-menu navbar-search-suggestion',
                            cursor: 'active',
                            suggestion: 'suggestion d-flex justify-content-between px-4 py-2 w-100'
                        }
                    },
                    // Páginas
                    {
                        name: 'pages',
                        display: 'name',
                        limit: 8,
                        source: filterConfig(searchData.pages),
                        templates: {
                            header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Páginas</h6>',
                            suggestion: function ({ url, icon, name }) {
                                return (
                                    '<a href="' +
                                    url +
                                    '">' +
                                    '<div>' +
                                    '<i class="ti ' +
                                    icon +
                                    ' me-2"></i>' +
                                    '<span class="align-middle">' +
                                    name +
                                    '</span>' +
                                    '</div>' +
                                    '</a>'
                                );
                            },
                            notFound:
                                '<div class="not-found px-4 py-2">' +
                                '<h6 class="suggestions-header text-primary mb-2">Páginas</h6>' +
                                '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> No se encontro resultados</p>' +
                                '</div>'
                        }
                    }
                )
                //On typeahead result render.
                .on('typeahead:render', function () {
                    // Show content backdrop,
                    contentBackdrop.addClass('show').removeClass('fade');
                })
                // On typeahead select
                .on('typeahead:select', function (ev, suggestion) {
                    // Open selected page
                    if (suggestion.url !== 'javascript:;') window.location = suggestion.url;
                })
                // On typeahead close
                .on('typeahead:close', function () {
                    // Clear search
                    searchInput.val('');
                    $this.typeahead('val', '');

                    // Hide search input wrapper
                    searchInputWrapper.addClass('d-none');

                    // Fade content backdrop
                    contentBackdrop.addClass('fade').removeClass('show');
                });

            // On searchInput keyup, Fade content backdrop if search input is blank
            searchInput.on('keyup', function () {
                if (searchInput.val() == '') contentBackdrop.addClass('fade').removeClass('show');
            });
        });

        // Init PerfectScrollbar in search result
        var psSearch;

        $('.navbar-search-suggestion').each(function () {
            psSearch = new PerfectScrollbar($(this)[0], {
                wheelPropagation: false,
                suppressScrollX: true
            });
        });

        searchInput.on('keyup', function () {
            psSearch.update();
        });
    }
});

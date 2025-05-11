/**
 * Search Navbar
 */

'use strict';

// ! Removed following code if you do't wish to use jQuery. Remember that navbar search functionality will stop working on removal.
if (typeof $ !== 'undefined') {
    $(function () {
        // ! TODO: Required to load after DOM is ready, did this now with jQuery ready.
        window.Helpers.initSidebarToggle();
        // Toggle Universal Sidebar

        // Navbar Search with autosuggest (typeahead)
        // ? You can remove the following JS if you don't want to use search functionality.
        //----------------------------------------------------------------------------------

        var searchToggler = $('.search-toggler'),
            searchInputWrapper = $('.search-input-wrapper'),
            searchInput = $('.search-input'),
            contentBackdrop = $('.content-backdrop');

        // Open search input on click of search icon
        if (searchToggler.length) {
            searchToggler.on('click', function () {
                if (searchInputWrapper.length) {
                    searchInputWrapper.toggleClass('d-none');
                    searchInput.focus();
                }
            });
        }
        // Open search on 'CTRL+/'
        $(document).on('keydown', function (event) {
            let ctrlKey = event.ctrlKey,
                slashKey = event.which === 191;

            if (ctrlKey && slashKey) {
                if (searchInputWrapper.length) {
                    searchInputWrapper.toggleClass('d-none');
                    searchInput.focus();
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

        if (searchInput.length) {
            // Filter config
            var filterConfig = function (data) {
                return function findMatches(q, cb) {
                    let matches;
                    matches = [];
                    data.filter(function (i) {
                        if (i.name.toLowerCase().startsWith(q.toLowerCase())) {
                            matches.push(i);
                        } else if (
                            !i.name.toLowerCase().startsWith(q.toLowerCase()) &&
                            i.name.toLowerCase().includes(q.toLowerCase())
                        ) {
                            matches.push(i);
                            matches.sort(function (a, b) {
                                return b.name < a.name ? 1 : -1;
                            });
                        } else {
                            return [];
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
                    url: assetsPath + '../../admin/navbar/' + searchJson, //? Use your own search api instead
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
                        // ? Add/Update blocks as per need
                        // Pages
                        {
                            name: 'pages',
                            display: 'name',
                            limit: 6,
                            source: filterConfig(searchData.pages),
                            templates: {
                                header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Pages</h6>',
                                suggestion: function ({ url, icon, name, path }) {
                                    return (
                                        '<a href="' +
                                        //baseUrl +
                                        url +
                                        '">' +
                                        '<div>' +
                                        '<i class="ti ' +
                                        icon +
                                        ' me-2"></i>' +
                                        '<span class="align-middle">' +
                                        path +
                                        ' / ' +
                                        name +
                                        '</span>' +
                                        '</div>' +
                                        '</a>'
                                    );
                                },
                                notFound:
                                    '<div class="not-found px-4 py-2">' +
                                    '<h6 class="suggestions-header text-primary mb-2">Pages</h6>' +
                                    '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> No se encontro resultados</p>' +
                                    '</div>'
                            }
                        },
                        // Categories
                        {
                            name: 'categories',
                            display: 'name',
                            limit: 6,
                            source: filterConfig(searchData.categories),
                            templates: {
                                header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Categorías</h6>',
                                suggestion: function ({ slug, icon, name, path }) {
                                    return (
                                        '<a href="' +
                                        //baseUrl +
                                        'admin/f/' +
                                        slug +
                                        '">' +
                                        '<div>' +
                                        '<i class="ti ' +
                                        icon +
                                        ' me-2"></i>' +
                                        '<span class="align-middle">' +
                                        path +
                                        (path? ' / ': '') +
                                        name +
                                        '</span>' +
                                        '</div>' +
                                        '</a>'
                                    );
                                },
                                notFound:
                                    '<div class="not-found px-4 py-2">' +
                                    '<h6 class="suggestions-header text-primary mb-2">Pages</h6>' +
                                    '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> No se encontro resultados</p>' +
                                    '</div>'
                            }
                        },
                        // Files
                        /*
                        {
                            name: 'files',
                            display: 'name',
                            limit: 4,
                            source: filterConfig(searchData.files),
                            templates: {
                                header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Files</h6>',
                                suggestion: function ({ src, name, subtitle, meta }) {
                                    return (
                                        '<a href="javascript:;">' +
                                        '<div class="d-flex w-50">' +
                                        '<img class="me-3" src="' +
                                        assetsPath +
                                        src +
                                        '" alt="' +
                                        name +
                                        '" height="32">' +
                                        '<div class="w-75">' +
                                        '<h6 class="mb-0">' +
                                        name +
                                        '</h6>' +
                                        '<small class="text-muted">' +
                                        subtitle +
                                        '</small>' +
                                        '</div>' +
                                        '</div>' +
                                        '<small class="text-muted">' +
                                        meta +
                                        '</small>' +
                                        '</a>'
                                    );
                                },
                                notFound:
                                    '<div class="not-found px-4 py-2">' +
                                    '<h6 class="suggestions-header text-primary mb-2">Files</h6>' +
                                    '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> No se encontro resultados</p>' +
                                    '</div>'
                            }
                        },
                        // Members
                        {
                            name: 'members',
                            display: 'name',
                            limit: 4,
                            source: filterConfig(searchData.members),
                            templates: {
                                header: '<h6 class="suggestions-header text-primary mb-0 mx-4 mt-3 pb-2">Members</h6>',
                                suggestion: function ({ name, src, subtitle }) {
                                    return (
                                        '<a href="' +
                                        baseUrl +
                                        'app/user/view/account">' +
                                        '<div class="d-flex align-items-center">' +
                                        '<img class="rounded-circle me-3" src="' +
                                        assetsPath +
                                        src +
                                        '" alt="' +
                                        name +
                                        '" height="32">' +
                                        '<div class="user-info">' +
                                        '<h6 class="mb-0">' +
                                        name +
                                        '</h6>' +
                                        '<small class="text-muted">' +
                                        subtitle +
                                        '</small>' +
                                        '</div>' +
                                        '</div>' +
                                        '</a>'
                                    );
                                },
                                notFound:
                                    '<div class="not-found px-4 py-2">' +
                                    '<h6 class="suggestions-header text-primary mb-2">Members</h6>' +
                                    '<p class="py-2 mb-0"><i class="ti ti-alert-circle ti-xs me-2"></i> No se encontro resultados</p>' +
                                    '</div>'
                            }
                        }
                        */
                    )
                    //On typeahead result render.
                    .bind('typeahead:render', function () {
                        // Show content backdrop,
                        contentBackdrop.addClass('show').removeClass('fade');
                    })
                    // On typeahead select
                    .bind('typeahead:select', function (ev, suggestion) {
                        // Open selected page
                        if (suggestion.url !== 'javascript:;') {
                            window.location = baseUrl + suggestion.url;
                        }
                    })
                    // On typeahead close
                    .bind('typeahead:close', function () {
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
                    if (searchInput.val() == '') {
                        contentBackdrop.addClass('fade').removeClass('show');
                    }
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
}

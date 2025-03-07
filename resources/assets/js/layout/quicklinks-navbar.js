/**
 * Quicklinks Navbar
 */

'use strict';

$(function () {
    // Navbar Quicklinks with autosuggest (typeahead)
    const $dropdownShortcuts = $('.dropdown-shortcuts'),
        $dropdownShortcutsAdd = $('.dropdown-shortcuts-add'),
        $dropdownShortcutsRemove = $('.dropdown-shortcuts-remove');

    const route = document.documentElement.getAttribute('data-route');

    if ($dropdownShortcuts.length) {
        $dropdownShortcutsAdd.on('click', function () {
            $.ajax({
                url: quicklinksUpdateUrl,
                method: 'POST',
                data: {
                    action: 'update',
                    route: route
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr) {
                    console.error(xhr.responseJSON.message);
                }
            });
        });

        $dropdownShortcutsRemove.on('click', function () {
            $.ajax({
                url: quicklinksUpdateUrl,
                method: 'POST',
                data: {
                    action: 'remove',
                    route: route
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr) {
                    console.error(xhr.responseJSON.message);
                }
            });
        });
    }
});

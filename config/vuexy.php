<?php

// Custom Config
// -------------------------------------------------------------------------------------
//! IMPORTANT: Make sure you clear the browser local storage In order to see the config changes in the template.
//! To clear local storage: (https://www.leadshook.com/help/how-to-clear-local-storage-in-google-chrome-browser/).

return [
    'custom' => [
        'myLayout' => 'horizontal', // Options[String]: vertical(default), horizontal
        'myTheme' => 'theme-semi-dark', // Options[String]: theme-default(default), theme-bordered, theme-semi-dark
        'myStyle' => 'light', // Options[String]: light(default), dark & system mode
        'myRTLSupport' => false, // options[Boolean]: true(default), false // To provide RTLSupport or not
        'myRTLMode' => false, // options[Boolean]: false(default), true // To set layout to RTL layout  (myRTLSupport must be true for rtl mode)
        'hasCustomizer' => true, // options[Boolean]: true(default), false // Display customizer or not THIS WILL REMOVE INCLUDED JS FILE. SO LOCAL STORAGE WON'T WORK
        'displayCustomizer' => true, // options[Boolean]: true(default), false // Display customizer UI or not, THIS WON'T REMOVE INCLUDED JS FILE. SO LOCAL STORAGE WILL WORK
        'contentLayout' => 'compact', // options[String]: 'compact', 'wide' (compact=container-xxl, wide=container-fluid)
        'navbarType' => 'static', // options[String]: 'sticky', 'static', 'hidden' (Only for vertical Layout)
        'footerFixed' => false, // options[Boolean]: false(default), true // Footer Fixed
        'menuFixed' => false, // options[Boolean]: true(default), false // Layout(menu) Fixed (Only for vertical Layout)
        'menuCollapsed' => true, // options[Boolean]: false(default), true // Show menu collapsed, (Only for vertical Layout)
        'headerType' => 'static', // options[String]: 'static', 'fixed' (for horizontal layout only)
        'showDropdownOnHover' => false, // true, false (for horizontal layout only)
        'authViewMode' => 'cover', // Options[String]: cover(default), basic
        'maxQuickLinks' => 8, // options[Integer]: 6(default), 8, 10
        'customizerControls' => [
            //'rtl',
            'style',
            'headerType',
            'contentLayout',
            'layoutCollapsed',
            'layoutNavbarOptions',
            'themes',
        ], // To show/hide customizer options
    ],
    'force_https' => env('FORCE_HTTPS', false),
];
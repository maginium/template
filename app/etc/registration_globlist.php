<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Glob Patterns for Registration Includes
|--------------------------------------------------------------------------
|
| This array contains glob patterns relative to the project's root directory.
| These patterns are used by the `registration.php` file to generate a list
| of includes for various files and resources throughout the project.
| Each glob pattern matches a specific location where Magento looks for
| registration files for modules, themes, language packs, and other
| components that need to be registered within the Magento system.
|
| The patterns below are designed to match specific paths in the app/code,
| app/design, app/i18n, lib/internal, and setup directories.
|
*/

return [
    // Matches all cli_commands.php files across modules in app/code
    'app/code/*/*/cli_commands.php',

    // Matches all registration.php files across modules in app/code
    'app/code/*/*/registration.php',

    // Matches all registration.php files across themes in app/design
    'app/design/*/*/*/registration.php',

    // Matches all registration.php files for language packs in app/i18n
    'app/i18n/*/*/registration.php',

    // Matches all registration.php files in the internal library directory
    'lib/internal/*/*/registration.php',

    // Matches all registration.php files in subdirectories under lib/internal
    'lib/internal/*/*/*/registration.php',

    // Matches all registration.php files in the setup source directory
    'setup/src/*/*/registration.php',
];

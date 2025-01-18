<?php

declare(strict_types=1);

/**
 * Path to Composer vendor directory.
 *
 * This configuration file returns the path to the `vendor` directory,
 * which is where Composer installs project dependencies. The `vendor` directory
 * typically contains all the third-party libraries required by the application,
 * as well as the Composer autoloader.
 *
 * The returned value can be used within the application to reference the
 * location of the Composer-managed dependencies for tasks such as autoloading
 * classes or handling dependency management.
 *
 * @return string The relative path to the Composer vendor directory.
 */
return './vendor';

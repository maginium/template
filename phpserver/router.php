<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Define Router Constants
|--------------------------------------------------------------------------
|
| This section defines various constants for the router. These constants
| are used globally within the app, ensuring consistency and easy management.
|
*/
define('DEBUG_ROUTER', false);

/**
 * Debug function to log values when DEBUG_ROUTER is enabled.
 * It logs to the PHP error log.
 *
 * @param mixed $value The value to log, could be a string or array.
 */
$debug = static function($value): void {
    if (! DEBUG_ROUTER) {
        // Check if debugging is disabled

        // Exit if debugging is not enabled
        return;
    }

    // Ensure that the value is a string, encoding arrays to JSON.
    $value = is_array($value) ? json_encode($value) : (string)$value;

    // Log the debug information to the PHP error log
    error_log("debug: {$value}");
};

/*
|--------------------------------------------------------------------------
| Request Handling for PHP Built-in Server
|--------------------------------------------------------------------------
|
| This section processes incoming requests and routes them according to the
| application’s needs, including serving static files, media, and dynamic content.
|
*/
if (php_sapi_name() === 'cli-server') {
    // Check if the script is being run by PHP's built-in server
    // Log the current request URI for debugging purposes
    $debug("URI: {$_SERVER['REQUEST_URI']}");

    // If the request is for a PHP script (index.php, get.php, static.php), pass it on to the built-in server
    if (preg_match('/^\/(index|get|static)\.php(\/)?/', $_SERVER['REQUEST_URI'])) {
        // Let the PHP built-in server handle the request if it is for a PHP script
        return false;
    }

    // Get the path information from the requested URI, and parse the URL
    $path = pathinfo($_SERVER['SCRIPT_FILENAME']);
    $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Extract file path details
    $pathinfo = pathinfo($route);

    // Get file extension, if available
    $ext = $pathinfo['extension'] ?? '';

    // Skip favicon.ico requests to avoid unnecessary processing
    if ($path['basename'] === 'favicon.ico') {
        // Return false to let the built-in server handle the favicon request
        return false;
    }

    // Log the route being processed for debugging
    $debug("Route: {$route}");

    /*
    |--------------------------------------------------------------------------
    | Handling Default Error Pages
    |--------------------------------------------------------------------------
    |
    | This section handles requests for the default error pages and modifies
    | the route to serve the correct error content.
    |
    */
    if (str_starts_with($route, 'pub/errors/default/')) {
        // Check if the route starts with 'pub/errors/default/'
        // Replace 'pub/errors/default/' with 'errors/default/' in the route for handling
        $route = preg_replace('#pub/errors/default/#', 'errors/default/', $route, 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Static and Media File Handling
    |--------------------------------------------------------------------------
    |
    | This section processes requests for static and media files, serving them
    | directly if they exist, or routing them through respective handlers.
    |
    */

    // Set the base directory for the 'pub' folder
    $magentoPackagePubDir = __DIR__ . '/../pub';

    if (str_starts_with($route, 'media/') ||
        str_starts_with($route, 'opt/') ||
        str_starts_with($route, 'static/') ||
        str_starts_with($route, 'errors/default/css/') ||
        str_starts_with($route, 'errors/default/images/')
    ) {
        // Check if the route is for media, static, or error pages

        // Construct the original file path from the route
        $origFile = "{$magentoPackagePubDir}/{$route}";

        // Handle static versioning paths (e.g., static/version123/assets)
        if (str_starts_with($route, 'static/version')) {
            // Clean versioning in static paths
            $route = preg_replace('#static/(version\d+/)?#', 'static/', $route, 1);
        }

        // Set the final file path
        $file = "{$magentoPackagePubDir}/{$route}";

        // Log the file path for debugging
        $debug("File: {$file}");

        // Check if the requested file exists in the directory
        if (file_exists($origFile) || file_exists($file)) {
            // Select the correct file (original or cleaned)
            $file = file_exists($origFile) ? $origFile : $file;

            // Log that the file exists and will be served
            $debug('File exists');

            // Define MIME types based on file extension for correct content type headers
            $mimeTypes = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'jpg' => 'image/jpg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'map' => 'application/json',
                'woff' => 'application/x-woff',
                'woff2' => 'application/font-woff2',
                'html' => 'text/html',
            ];

            // Set the appropriate content type header for the file being served
            if (isset($mimeTypes[$ext])) {
                header("Content-Type: {$mimeTypes[$ext]}");
            }

            // Serve the file content
            readfile($file);

            // Exit after serving the file
            return;
        }
        // Log if the file does not exist
        $debug('File does not exist');

        // If the file is a static or media request, process it through PHP handlers
        if (str_starts_with($route, 'static/')) {
            // Check if the route is for static files

            // Clean the static path
            $route = preg_replace('#static/#', '', $route, 1);

            // Pass the resource as a query parameter
            $_GET['resource'] = $route;

            // Log the static resource being processed
            $debug("Static: {$route}");

            // Include the static handler PHP file
            include "{$magentoPackagePubDir}/static.php";

            // Exit after handling the static file
            exit;
        }

        if (str_starts_with($route, 'media/')) {
            // Check if the route is for media files

            // Log the media resource being processed
            $debug("Media: {$route}");

            // Include the media handler PHP file
            include "{$magentoPackagePubDir}/get.php";

            // Exit after handling the media file
            exit;
        }
    } else {
        // If the route does not match static or media paths, serve the default index.php page

        // Log that the route falls back to index.php
        $debug("Route falls back to index.php for {$route}");

        // Include the default handler PHP file
        include "{$magentoPackagePubDir}/index.php";
    }
}

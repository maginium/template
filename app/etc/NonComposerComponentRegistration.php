<?php

declare(strict_types=1);

/*
 *
 *  🚀 This file is part of the Maginium Framework.
 *
 *  ©️ 2025. Pixielity ©. Technologies <contact@maginium>
 *  🖋️ Author: Abdelrhman Kouta
 *      - 📧 Email: pixiedia@gmail.com
 *      - 🌐 Website: https://maginium.com
 *  📖 Documentation: https://docs.maginium.com
 *
 *  📄 For the full copyright and license information, please view
 *  the LICENSE file that was distributed with this source code.
 */

namespace App;

use Dotenv\Dotenv;
use RuntimeException;
use Symfony\Component\Finder\Finder;

use function dirname;

/**
 * Class NonComposerComponentRegistration.
 *
 * Handles the dynamic registration of components by searching for and including
 * 'registration.php' files located in specified directories. Uses Symfony Finder
 * to locate files and Dotenv for environment variable management.
 */
class NonComposerComponentRegistration
{
    /**
     * @var string Base directory from which to start searching for components.
     */
    private string $baseDir;

    /**
     * @var Finder Symfony Finder instance for file search functionality.
     */
    private Finder $finder;

    /**
     * @var array|string[] Array of glob patterns that specify directories to search.
     */
    private array $globPatterns;

    /**
     * NonComposerComponentRegistration constructor.
     *
     * Initializes the base directory, loads environment variables, and sets up
     * the Symfony Finder instance for searching 'registration.php' files.
     *
     * @param array|string[] $globPatterns Array of glob patterns to search for components.
     */
    public function __construct(array $globPatterns)
    {
        // Set the base directory two levels up from the current directory.
        $this->baseDir = dirname(__DIR__, 2) . SP;

        // Load environment variables from the .env file in the base directory.
        $dotenv = Dotenv::createImmutable($this->baseDir);
        $dotenv->load();

        $this->finder = new Finder;

        // Store the provided glob patterns to later search for component registrations.
        $this->globPatterns = $globPatterns;
    }

    /**
     * Registers components by finding and including 'registration.php' files based on
     * the specified glob patterns.
     *
     * If no files are found matching the glob patterns, a RuntimeException is thrown.
     *
     * @throws RuntimeException If no 'registration.php' files are found.
     */
    public function registerModules(): void
    {
        // Iterate over each glob pattern and configure Finder to search in relevant directories.
        foreach ($this->globPatterns as $globPattern) {
            $this->configureFinderFromPattern($globPattern);
        }

        // Throw an error if no files were found.
        if (! $this->finder->hasResults()) {
            $this->debug("Error: No 'registration.php' files found matching the glob patterns.");
        }

        // Include each located 'registration.php' file.
        foreach ($this->finder as $file) {
            $filePath = $file->getRealPath();
            // $this->debug("Including file: {$filePath}");

            require_once $filePath;
        }
    }

    /**
     * Configures the Finder to search for 'registration.php' files in the directory specified by a glob pattern.
     *
     * Extracts the base directory from the glob pattern, and if the directory exists, sets the Finder to search
     * for the 'registration.php' files within that directory.
     *
     * @param string $globPattern The glob pattern specifying directories to search.
     */
    private function configureFinderFromPattern(string $globPattern): void
    {
        // Extract the base directory from the glob pattern.
        $basePatternDir = $this->extractBaseDirectory($globPattern);

        // Concatenate the base directory with the pattern-specific directory.
        $fullDir = $this->baseDir . $basePatternDir;

        // Check if the directory exists before configuring Finder to search within it.
        if (is_dir($fullDir)) {
            $this->finder->files()
                ->in($fullDir)
                ->name('registration.php');
        } else {
            $this->debug("Warning: Directory does not exist: {$fullDir}");
        }
    }

    /**
     * Extracts the base directory from a glob pattern by removing the wildcard portions (e.g., * or **).
     *
     * This limits the search scope to a specific directory before reaching any wildcards.
     *
     * @param string $globPattern The glob pattern used to identify directories.
     *
     * @return string The base directory without the wildcard portion.
     */
    private function extractBaseDirectory(string $globPattern): string
    {
        // Split the pattern into parts and collect directory segments before hitting wildcards.
        $patternParts = explode(SP, $globPattern);
        $baseDirParts = [];

        foreach ($patternParts as $part) {
            if ($part === '*' || $part === '**') {
                // Stop collecting parts once a wildcard is encountered.
                break;
            }
            $baseDirParts[] = $part;
        }

        // Reassemble the base directory from the parts.
        $baseDir = implode(SP, $baseDirParts);

        return $baseDir;
    }

    /**
     * Debugging helper function for outputting debug messages if APP_DEBUG is set to true.
     *
     * @param string $message The debug message to output.
     */
    private function debug(string $message): void
    {
        // Check if debugging is enabled via the APP_DEBUG environment variable.
        if (! $_ENV['APP_DEBUG']) {
            return;
        }

        // Output the debug message.
        // echo "{$message}\n";
    }
}

/**
 * Main function to execute the component registration process.
 *
 * Defines the finder patterns for locating 'registration.php' files and
 * invokes the registration functionality in the NonComposerComponentRegistration class.
 */
function execute(): void
{
    // Define the glob patterns from 'registration_globlist' file.
    $globPatterns = require __DIR__ . SP . 'registration_globlist.php';

    // Instantiate the component registrar with the defined glob patterns.
    $registrar = new NonComposerComponentRegistration($globPatterns);

    // Execute the registration process to include found components.
    $registrar->registerModules();
}

// Call the main function to start component registration.
execute();

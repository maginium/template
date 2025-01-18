<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Filesystems Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains the configuration for various filesystem disks
    | used in the application. The configuration defines drivers, root paths,
    | visibility settings, and other details for handling storage.
    | Supported drivers include 'local' and 's3', among others.
    |
    */
    'filesystems' => [
        'default' => env('FILESYSTEM_DEFAULT', 'local'),
        'cloud' => env('FILESYSTEM_CLOUD', 's3'),
        'local' => [
            'driver' => 'local',
            'root' => env('FILESYSTEM_LOCAL_ROOT', 'pub'),
            'throw' => env('FILESYSTEM_LOCAL_THROW', false),
        ],
        'public' => [
            'driver' => 'local',
            'root' => env('FILESYSTEM_PUBLIC_ROOT', 'pub'),
            'url' => env('FILESYSTEM_PUBLIC_URL', 'https://example.com/pub'),
            'visibility' => env('FILESYSTEM_PUBLIC_VISIBILITY', 'public'),
            'throw' => env('FILESYSTEM_PUBLIC_THROW', false),
        ],
        'uploads' => [
            'driver' => 'local',
            'root' => env('FILESYSTEM_UPLOADS_ROOT', 'pub/uploads'),
            'url' => env('FILESYSTEM_UPLOADS_URL', '/pub/uploads'),
            'visibility' => env('FILESYSTEM_UPLOADS_VISIBILITY', 'public'),
            'throw' => env('FILESYSTEM_UPLOADS_THROW', false),
        ],
        'media' => [
            'driver' => 'local',
            'root' => env('FILESYSTEM_MEDIA_ROOT', 'pub/media'),
            'url' => env('FILESYSTEM_MEDIA_URL', '/pub/media'),
            'visibility' => env('FILESYSTEM_MEDIA_VISIBILITY', 'public'),
            'throw' => env('FILESYSTEM_MEDIA_THROW', false),
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', ''),
            'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_S3_BUCKET', ''),
            'url' => env('AWS_S3_URL', 'https://s3.amazonaws.com'),
            'endpoint' => env('AWS_S3_ENDPOINT', 'https://s3.us-east-1.amazonaws.com'),
            'use_path_style_endpoint' => env('AWS_S3_PATH_STYLE', true),
            'throw' => env('AWS_S3_THROW', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backend Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines the backend configuration options for the admin
    | panel. It includes the frontName, which determines the base URL for
    | accessing the admin panel.
    |
    */
    'backend' => [
        'frontName' => env('BACKEND_FRONT_NAME', 'admin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for the application, including default cache backend,
    | frontend options, and settings for parallel generation.
    |
    */
    'cache' => [
        'default' => env('CACHE_CONNECTION', 'default'),
        'frontend' => [
            'default' => [
                'id_prefix' => env('CACHE_ID_PREFIX', 'ef1_'),
                'backend' => env('CACHE_BACKEND', 'Cm_Cache_Backend_Redis'),
                'backend_options' => [
                    'server' => env('CACHE_SERVER', 'localhost'),
                    'port' => env('CACHE_PORT', '6379'),
                    'persistent' => env('CACHE_PERSISTENT', ''),
                    'database' => env('CACHE_DATABASE', '0'),
                    'password' => env('CACHE_PASSWORD', ''),
                    'lock_connection' => env('CACHE_LOCK_CONNECTION', ''),
                    'force_standalone' => env('CACHE_FORCE_STANDALONE', '0'),
                    'connect_retries' => env('CACHE_CONNECT_RETRIES', '3'),
                    'read_timeout' => env('CACHE_READ_TIMEOUT', '10'),
                    'automatic_cleaning_factor' => env('CACHE_AUTOMATIC_CLEANING_FACTOR', '0'),
                    'compress_data' => env('CACHE_COMPRESS_DATA', '1'),
                    'compress_tags' => env('CACHE_COMPRESS_TAGS', '1'),
                    'compress_threshold' => env('CACHE_COMPRESS_THRESHOLD', '20480'),
                    'compression_lib' => env('CACHE_COMPRESSION_LIB', 'gzip'),
                    'use_lua' => env('CACHE_USE_LUA', '0'),
                ],
            ],
            'page_cache' => [
                'id_prefix' => env('PAGE_CACHE_ID_PREFIX', 'ef1_'),
                'backend' => env('PAGE_CACHE_BACKEND', 'Cm_Cache_Backend_Redis'),
                'backend_options' => [
                    'server' => env('PAGE_CACHE_SERVER', 'localhost'),
                    'port' => env('PAGE_CACHE_PORT', '6379'),
                    'database' => env('PAGE_CACHE_DATABASE', '1'),
                    'compress_data' => env('PAGE_CACHE_COMPRESS_DATA', '0'),
                    'persistent' => env('PAGE_CACHE_PERSISTENT', ''),
                    'password' => env('PAGE_CACHE_PASSWORD', ''),
                    'force_standalone' => env('PAGE_CACHE_FORCE_STANDALONE', '0'),
                    'connect_retries' => env('PAGE_CACHE_CONNECT_RETRIES', '1'),
                    'lifetimelimit' => env('PAGE_CACHE_LIFETIME_LIMIT', '57600'),
                ],
            ],
        ],
        'ALLOW_PARALLEL_GENERATION' => env('CACHE_ALLOW_PARALLEL_GENERATION', false),
        'allow_parallel_generation' => env('CACHE_ALLOW_PARALLEL_GENERATION', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Defines the database connection settings for the application.
    | Supports MySQL with options for host, database name, username, and
    | other driver-specific configurations.
    |
    */
    'db' => [
        'default' => env('DB_CONNECTION', 'default'),
        'table_prefix' => env('DB_TABLE_PREFIX', ''),
        'connection' => [
            'default' => [
                'host' => env('DB_HOST', 'localhost'),
                'dbname' => env('DB_NAME', 'default'),
                'username' => env('DB_USERNAME', 'default'),
                'password' => env('DB_PASSWORD', 'default'),
                'model' => env('DB_MODEL', 'mysql4'),
                'engine' => env('DB_ENGINE', 'innodb'),
                'initStatements' => env('DB_INIT_STATEMENTS', 'SET NAMES utf8;'),
                'active' => env('DB_ACTIVE', '1'),
                'driver_options' => [
                    1014 => env('DB_DRIVER_OPTION_1014', false),
                ],
                'charset' => env('DB_CHARSET', 'utf8mb4'),
                'driver' => env('DB_DRIVER', 'mysql'),
                'prefix' => env('DB_PREFIX', ''),
                'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            ],
        ],
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default',
        ],
    ],

    /*
        |--------------------------------------------------------------------------
        | Queue Configuration
        |--------------------------------------------------------------------------
        |
        | The queue configuration defines the settings for AMQP connections and
        | consumer behavior. The `amqp` section includes connection details
        | for RabbitMQ, while `consumers_wait_for_messages` determines whether
        | consumers wait for new messages to process.
        |
        */
    'queue' => [
        'amqp' => [
            'host' => env('QUEUE_AMQP_HOST', 'localhost'),
            'port' => env('QUEUE_AMQP_PORT', '5672'),
            'user' => env('QUEUE_AMQP_USER', 'guest'),
            'password' => env('QUEUE_AMQP_PASSWORD', 'guest'),
            'virtualhost' => env('QUEUE_AMQP_VIRTUALHOST', '/'),
            'ssl' => env('QUEUE_AMQP_SSL', ''),
        ],
        'consumers_wait_for_messages' => env('QUEUE_CONSUMERS_WAIT_FOR_MESSAGES', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cron Consumers Runner Configuration
    |--------------------------------------------------------------------------
    |
    | The `cron_consumers_runner` section defines the configuration for running
    | cron jobs related to message consumers. This configuration allows you to
    | control the behavior of cron jobs, such as whether they should run, the
    | maximum number of messages they should process, and the list of consumers
    | to be executed.
    |
    | - `cron_run`: Whether cron jobs should run (`true` or `false`).
    | - `max_messages`: The maximum number of messages to process per cron run.
    | - `consumers`: A list of consumers to be processed by the cron jobs.
    |
    */
    'cron_consumers_runner' => [
        'cron_run' => true,
        'max_messages' => 10000,
        'consumers' => [
            'async.operations.all',
            'email.message.consumer',
            'email.message.delay.consumer',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines session settings, including storage mechanism,
    | Redis-specific options, and session lifetimes.
    |
    */
    'session' => [
        'save' => env('SESSION_SAVE', 'redis'),
        'redis' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'port' => env('REDIS_PORT', '6379'),
            'password' => env('REDIS_PASSWORD', ''),
            'timeout' => env('REDIS_TIMEOUT', '2.5'),
            'persistent_identifier' => env('REDIS_PERSISTENT_IDENTIFIER', ''),
            'database' => env('REDIS_DATABASE', '2'),
            'compression_threshold' => env('REDIS_COMPRESSION_THRESHOLD', '2048'),
            'compression_library' => env('REDIS_COMPRESSION_LIBRARY', 'gzip'),
            'log_level' => env('REDIS_LOG_LEVEL', '1'),
            'max_concurrency' => env('REDIS_MAX_CONCURRENCY', '6'),
            'break_after_frontend' => env('REDIS_BREAK_AFTER_FRONTEND', '5'),
            'break_after_adminhtml' => env('REDIS_BREAK_AFTER_ADMINHTML', '30'),
            'first_lifetime' => env('REDIS_FIRST_LIFETIME', '600'),
            'bot_first_lifetime' => env('REDIS_BOT_FIRST_LIFETIME', '60'),
            'bot_lifetime' => env('REDIS_BOT_LIFETIME', '7200'),
            'disable_locking' => env('REDIS_DISABLE_LOCKING', '0'),
            'min_lifetime' => env('REDIS_MIN_LIFETIME', '60'),
            'max_lifetime' => env('REDIS_MAX_LIFETIME', '2592000'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | X-Frame-Options header controls whether the browser should allow
    | the application to be displayed in an iframe. The default value is
    | 'SAMEORIGIN', which restricts framing to the same origin.
    |
    */
    'x-frame-options' => env('X_FRAME_OPTIONS', 'SAMEORIGIN'),

    /*
    |--------------------------------------------------------------------------
    | Magento Mode
    |--------------------------------------------------------------------------
    |
    | Sets the Magento application mode. Available modes are:
    | - developer
    | - production
    | - default
    |
    */
    'MAGE_MODE' => env('APP_ENV', 'developer'),

    /*
    |--------------------------------------------------------------------------
    | Lock Provider
    |--------------------------------------------------------------------------
    |
    | Determines the provider for lock mechanisms within the application.
    | Options include 'db' or other lock service providers.
    |
    */
    'lock' => [
        'provider' => env('LOCK_PROVIDER', 'db'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Directories Configuration
    |--------------------------------------------------------------------------
    |
    | Specifies if the document root for the application is the `pub` folder.
    |
    */
    'directories' => [
        'document_root_is_pub' => env('DOCUMENT_ROOT_IS_PUB', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Types Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific cache types. A value of `1` enables the cache,
    | while `0` disables it.
    |
    */
    'cache_types' => [
        'config' => env('CACHE_TYPE_CONFIG', 1),
        'layout' => env('CACHE_TYPE_LAYOUT', 1),
        'block_html' => env('CACHE_TYPE_BLOCK_HTML', 0),
        'collections' => env('CACHE_TYPE_COLLECTIONS', 1),
        'reflection' => env('CACHE_TYPE_REFLECTION', 1),
        'db_ddl' => env('CACHE_TYPE_DB_DDL', 1),
        'compiled_config' => env('CACHE_TYPE_COMPILED_CONFIG', 1),
        'eav' => env('CACHE_TYPE_EAV', 1),
        'customer_notification' => env('CACHE_TYPE_CUSTOMER_NOTIFICATION', 1),
        'config_integration' => env('CACHE_TYPE_CONFIG_INTEGRATION', 1),
        'config_integration_api' => env('CACHE_TYPE_CONFIG_INTEGRATION_API', 1),
        'full_page' => env('CACHE_TYPE_FULL_PAGE', 0),
        'config_webservice' => env('CACHE_TYPE_CONFIG_WEBSERVICE', 1),
        'translate' => env('CACHE_TYPE_TRANSLATE', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Downloadable Domains
    |--------------------------------------------------------------------------
    |
    | Specifies a list of domains that are allowed to serve downloadable content.
    |
    */
    'downloadable_domains' => explode(',', env('DOWNLOADABLE_DOMAINS', 'examples.test')),

    /*
    |--------------------------------------------------------------------------
    | Installation Date
    |--------------------------------------------------------------------------
    |
    | The installation date of the Magento application.
    |
    */
    'install' => [
        'date' => env('INSTALL_DATE', 'Sat, 04 Jan 2025 16:24:23 +0000'),
    ],

    /*
        |--------------------------------------------------------------------------
        | Cryptography Configuration
        |--------------------------------------------------------------------------
        |
        | This configuration contains the cryptographic key used for encrypting
        | sensitive data in the application. The key should be stored securely
        | and not hardcoded in the application.
        |
        */
    'crypt' => [
        'key' => env('APP_KEY', '123456'),
    ],

    /*
    |--------------------------------------------------------------------------
    | General Configuration
    |--------------------------------------------------------------------------
    |
    | This section contains general application configuration settings.
    | These settings are designed to control core application behaviors,
    | such as asynchronous operations. Using environment variables allows
    | you to manage these settings across different environments easily.
    |
    */
    'config' => [
        'async' => env('CONFIG_ASYNC', 0),
    ],
];

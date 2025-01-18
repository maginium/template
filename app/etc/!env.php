<?php

declare(strict_types=1);

return [
    'backend' => [
        'frontName' => 'backend',
    ],
    'remote_storage' => [
        'driver' => 'file',
    ],
    'cache' => [
        'graphql' => [
            'id_salt' => 'abpuosV89c760Mx1BzXZavrHqB4LUD4K',
        ],
        'frontend' => [
            'default' => [
                'id_prefix' => '113_',
            ],
            'page_cache' => [
                'id_prefix' => '113_',
            ],
        ],
        'allow_parallel_generation' => false,
    ],
    'config' => [
        'async' => 0,
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1,
    ],
    'crypt' => [
        'key' => 'base64GCZDdyrB7VTalHG6YVgB+lVBLpJYlvfqLbFnG2PiJv8=',
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'mariadb.dropshipping.orb.local',
                'dbname' => 'pixicommerce',
                'username' => 'pixicommerce',
                'password' => 'pixicommerce',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false,
                ],
            ],
        ],
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default',
        ],
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files',
    ],
    'lock' => [
        'provider' => 'db',
    ],
    'directories' => [
        'document_root_is_pub' => true,
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'graphql_query_resolver_result' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
    ],
    'downloadable_domains' => [
        'maginium.test',
    ],
    'install' => [
        'date' => 'Sat, 18 Jan 2025 01:23:19 +0000',
    ],
];

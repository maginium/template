<?php

declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Validator;

use Elasticsearch\ClientBuilder;
use Exception;
use Magento\Setup\Exception as SetupException;

/**
 * Connection validator for Elasticsearch configuration.
 */
class ElasticsearchConnectionValidator
{
    /**
     * Validate elasticsearch connection.
     *
     * Throws exception if unable to connect to Elasticsearch server
     *
     * @param array $options
     *
     * @throws Exception
     *
     * @return bool
     */
    public function isValidConnection(array $options)
    {
        $config = $this->buildConfig($options);

        $elasticsearchClient = ClientBuilder::fromConfig($config, true);
        $elasticsearchClient->ping();

        return true;
    }

    /**
     * Construct elasticsearch connection string.
     *
     * @param array $options
     *
     * @throws SetupException
     *
     * @return array
     */
    private function buildConfig(array $options)
    {
        $hostname = preg_replace('/http[s]?:\/\//i', '', $options['hostname']);
        // @codingStandardsIgnoreStart
        $protocol = parse_url($options['hostname'], PHP_URL_SCHEME);

        // @codingStandardsIgnoreEnd
        if (! $protocol) {
            $protocol = 'http';
        }

        $authString = '';

        if (isset($options['enableAuth']) && $options['enableAuth'] === true) {
            if (empty($options['username']) || empty($options['password'])) {
                throw new SetupException(
                    'Search engine misconfiguration. Username and password must be set if authentication is enabled',
                );
            }
            $authString = "{$options['username']}:{$options['password']}@";
        }
        $portString = empty($options['port']) ? '' : ':' . $options['port'];
        $host = $protocol . '://' . $authString . $hostname . $portString;
        $options['hosts'] = [$host];

        return $options;
    }
}

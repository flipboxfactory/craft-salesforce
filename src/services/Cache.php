<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\services;

use flipbox\craft\integration\services\IntegrationCache;
use flipbox\craft\salesforce\Force;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Cache extends IntegrationCache
{
    /**
     * The override file
     */
    public $overrideFile = 'salesforce-cache';

    /**
     * @inheritdoc
     */
    protected function getDefaultCache(): string
    {
        return Force::getInstance()->getSettings()->getDefaultCache();
    }

    /**
     * @inheritdoc
     */
    protected function handleCacheNotFound(string $handle)
    {
        Force::warning(sprintf(
            "Unable to find cache '%s'.",
            $handle
        ));

        return parent::handleCacheNotFound($handle);
    }
}

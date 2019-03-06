<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use flipbox\craft\salesforce\Force;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait CacheTrait
{
    /**
     * @param null|string|CacheInterface $cache
     * @return CacheInterface
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolveCache($cache): CacheInterface
    {
        if ($cache instanceof CacheInterface) {
            return $cache;
        }

        if ($cache === null) {
            $cache = Force::getInstance()->getSettings()->getDefaultCache();
        }

        return Force::getInstance()->getCache()->get($cache);
    }
}

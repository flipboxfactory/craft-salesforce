<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/salesforce/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/salesforce
 */

namespace flipbox\craft\salesforce\helpers;

use craft\helpers\StringHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.2
 */
class ObjectHelper
{
    /**
     * @param string $objectId
     * @return bool
     */
    public static function isCaseSafeObjectId(string $objectId): bool
    {
        return StringHelper::length($objectId) === 18;
    }
}

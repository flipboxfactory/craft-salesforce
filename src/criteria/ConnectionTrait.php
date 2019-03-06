<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use flipbox\craft\salesforce\Force;
use Flipbox\Salesforce\Connections\ConnectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ConnectionTrait
{
    /**
     * @param $connection
     * @return ConnectionInterface
     * @throws \flipbox\craft\integration\exceptions\ConnectionNotFound
     */
    protected function resolveConnection($connection): ConnectionInterface
    {
        if ($connection instanceof ConnectionInterface) {
            return $connection;
        }

        if ($connection === null) {
            $connection = Force::getInstance()->getSettings()->getDefaultConnection();
        }

        return Force::getInstance()->getConnections()->get($connection);
    }
}

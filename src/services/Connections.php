<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\services;

use flipbox\craft\integration\services\IntegrationConnections;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\records\Connection;
use Flipbox\Salesforce\Connections\ConnectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Connections extends IntegrationConnections
{
    /**
     * The override file
     */
    public $overrideFile = 'salesforce-connections';

    /**
     * @inheritdoc
     */
    protected static function tableName(): string
    {
        return Connection::tableName();
    }

    /**
     * @inheritdoc
     */
    protected static function connectionInstance(): string
    {
        return ConnectionInterface::class;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultConnection(): string
    {
        return Force::getInstance()->getSettings()->getDefaultConnection();
    }
}

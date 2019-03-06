<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\events;

use flipbox\craft\salesforce\connections\SavableConnectionInterface;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterConnectionsEvent extends Event
{
    /**
     * Event to register connections
     */
    const REGISTER_CONNECTIONS = 'registerConnectionTypes';

    /**
     * @var SavableConnectionInterface[]
     */
    public $connections = [];
}

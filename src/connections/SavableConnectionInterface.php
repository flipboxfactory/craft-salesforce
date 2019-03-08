<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\connections;

use flipbox\craft\integration\connections\SavableConnectionInterface as BaseSavableConnectionInterface;
use Flipbox\Salesforce\Connections\ConnectionInterface;

interface SavableConnectionInterface extends ConnectionInterface, BaseSavableConnectionInterface
{
}

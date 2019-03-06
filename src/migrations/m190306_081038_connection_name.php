<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\migrations;

use flipbox\craft\salesforce\records\Connection as ConnectionRecord;
use flipbox\craft\integration\migrations\IntegrationConnectionNameColumn;

class m190306_081038_connection_name extends IntegrationConnectionNameColumn
{
    /**
     * @return string
     */
    protected static function tableName(): string
    {
        return ConnectionRecord::tableName();
    }
}

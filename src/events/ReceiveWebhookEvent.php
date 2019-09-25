<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\events;

use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
class ReceiveWebhookEvent extends Event
{
    /**
     * @var array
     */
    public $data;
}

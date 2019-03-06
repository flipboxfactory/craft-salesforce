<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use flipbox\craft\ember\objects\ElementAttributeTrait;
use flipbox\craft\ember\objects\FieldAttributeTrait;
use flipbox\craft\ember\objects\SiteAttributeTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectCriteria extends \Flipbox\Salesforce\Criteria\ObjectCriteria
{
    use ConnectionTrait,
        CacheTrait,
        IdAttributeFromElementTrait,
        PayloadAttributeFromElementTrait,
        ElementAttributeTrait,
        FieldAttributeTrait,
        SiteAttributeTrait;
}

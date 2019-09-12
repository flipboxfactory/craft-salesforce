<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use flipbox\craft\salesforce\records\SOQL;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class QueryCriteria extends \Flipbox\Salesforce\Criteria\QueryCriteria
{
    use ConnectionTrait,
        CacheTrait;

    /**
     * @param string $handle
     * @return $this
     */
    public function setHandle(string $handle)
    {
        if(null !== ($record = SOQL::findOne(['handle' => $handle]))) {
            $this->setQuery($record);
        }

        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        if(null !== ($record = SOQL::findOne(['id' => $id]))) {
            $this->setQuery($record);
        }

        return $this;
    }
}

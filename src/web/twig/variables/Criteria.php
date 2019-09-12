<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\web\twig\variables;

use flipbox\craft\salesforce\criteria\ObjectCriteria;
use flipbox\craft\salesforce\criteria\QueryCriteria;
use flipbox\craft\salesforce\criteria\SearchCriteria;
use flipbox\craft\salesforce\criteria\UrlCriteria;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.3
 */
class Criteria extends Component
{
    /**
     * @param array $properties
     * @return UrlCriteria
     */
    public function getUrl(array $properties = []): UrlCriteria
    {
        $criteria = (new UrlCriteria())
            ->populate($properties);

        return $criteria;
    }

    /**
     * @param array $properties
     * @return QueryCriteria
     */
    public function getQuery(array $properties = []): QueryCriteria
    {
        $criteria = (new QueryCriteria())
            ->populate($properties);

        return $criteria;
    }

    /**
     * @param array $properties
     * @return SearchCriteria
     */
    public function getSearch(array $properties = []): SearchCriteria
    {
        $criteria = (new SearchCriteria())
            ->populate($properties);

        return $criteria;
    }

    /**
     * @param array $properties
     * @return ObjectCriteria
     */
    public function getObject(array $properties = []): ObjectCriteria
    {
        $criteria = (new ObjectCriteria())
            ->populate($properties);

        return $criteria;
    }
}

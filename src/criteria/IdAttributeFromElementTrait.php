<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\criteria;

use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\salesforce\records\ObjectAssociation;
use Flipbox\HubSpot\Criteria\IdAttributeTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 */
trait IdAttributeFromElementTrait
{
    use IdAttributeTrait;

    /**
     * @return int|null
     */
    abstract public function getFieldId();

    /**
     * @return int|null
     */
    abstract public function getElementId();

    /**
     * @return int|null
     */
    abstract public function getSiteId();

    /**
     * @return string|null
     */
    public function findId()
    {
        if (null === $this->id) {
            $this->id = $this->resolveId();
        }

        return $this->id;
    }

    /**
     * @return string|null
     */
    protected function resolveId()
    {
        $fieldId = $this->getFieldId();
        $elementId = $this->getElementId();

        if (null === $fieldId || null === $elementId) {
            return null;
        }

        if (!$objectId = ObjectAssociation::find()
            ->select(['objectId'])
            ->field($fieldId)
            ->element($elementId)
            ->siteId(SiteHelper::ensureSiteId($this->getSiteId()))
            ->scalar()) {
            return null;
        }

        return (string)$objectId;
    }
}

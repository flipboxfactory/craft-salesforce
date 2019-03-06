<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\queue;

use Craft;
use craft\queue\BaseJob;
use flipbox\craft\ember\objects\ElementAttributeTrait;
use flipbox\craft\ember\objects\FieldAttributeTrait;
use flipbox\craft\salesforce\fields\Objects;

/**
 * Sync a Craft Element to a Salesforce Object
 */
class SyncElementToSalesforceObjectJob extends BaseJob implements \Serializable
{
    use FieldAttributeTrait,
        ElementAttributeTrait;

    /**
     * @var string|null
     */
    public $objectId;

    /**
     * @var callable|array|string
     */
    public $transformer;

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @inheritdoc
     * @return bool
     */
    public function execute($queue)
    {
        $field = $this->getField();

        if (!$field instanceof Objects) {
            return false;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return $field->syncToSalesforce(
            $this->getElement(),
            $this->objectId,
            $this->transformer
        );
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            'fieldId' => $this->getFieldId(),
            'elementId' => $this->getElementId(),
            'objectId' => $this->objectId,
            'transformer' => $this->transformer
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        Craft::configure(
            $this,
            unserialize($serialized)
        );
    }
}

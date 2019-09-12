<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\integration\fields\actions\AbstractIntegrationAction;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\Force;
use yii\web\HttpException;

class SyncTo extends AbstractIntegrationAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Force::t('Create Salesforce Object from Element');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Force::t(
            "This element will be used to create a new Salesforce Object.  Please confirm to continue."
        );
    }

    /**
     * @inheritdoc
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function performAction(Integrations $field, ElementInterface $element): bool
    {
        if (!$field instanceof Objects) {
            $this->setMessage("Invalid field type.");
            return false;
        }

        /** @var IntegrationAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Field is not associated to element');
        }

        if (!$field->syncToSalesforce($element)) {
            $this->setMessage("Failed to create Salesforce " . $field->getObjectLabel());
            return false;
        }

        $this->id = $query->select(['objectId'])->scalar();

        $this->setMessage("Created Salesforce Object successfully");
        return true;
    }
}

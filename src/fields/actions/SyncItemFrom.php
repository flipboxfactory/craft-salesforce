<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\integration\fields\actions\AbstractIntegrationItemAction;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\Force;

class SyncItemFrom extends AbstractIntegrationItemAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Force::t('Sync From Salesforce');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Force::t("Performing a sync will override any unsaved data.  Please confirm to continue.");
    }

    /**
     * @inheritdoc
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function performAction(Integrations $field, ElementInterface $element, IntegrationAssociation $record): bool
    {
        if (!$field instanceof Objects) {
            $this->setMessage("Invalid field type.");
            return false;
        }

        if (!$field->syncFromSalesforce($element)) {
            $this->setMessage("Failed to sync from Salesforce " . $field->getObjectLabel());
            return false;
        }

        $this->setMessage("Sync from Salesforce executed successfully");
        return true;
    }
}

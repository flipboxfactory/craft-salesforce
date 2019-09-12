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

class SyncItemTo extends AbstractIntegrationItemAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Force::t('Sync To Salesforce');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Force::t("Performing a sync will transmit any unsaved data.  Please confirm to continue.");
    }

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function performAction(Integrations $field, ElementInterface $element, IntegrationAssociation $record): bool
    {
        if (!$field instanceof Objects) {
            $this->setMessage("Invalid field type.");
            return false;
        }

        if (!$field->syncToSalesforce($element)) {
            $this->setMessage("Failed to sync to Salesforce " . $field->getObjectLabel());
            return false;
        }

        $this->setMessage("Sync to Salesforce executed successfully");
        return true;
    }
}

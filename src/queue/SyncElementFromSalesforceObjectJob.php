<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\queue;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\ArrayHelper;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\transformers\PopulateElementErrorsFromResponse;
use flipbox\craft\salesforce\transformers\PopulateElementFromResponse;
use Flipbox\Salesforce\Resources\SObject;

/**
 * Sync a Salesforce Object to a Craft Element
 */
class SyncElementFromSalesforceObjectJob extends AbstractSyncElementJob
{
    use ResolveObjectIdFromElementTrait;

    /**
     * @var string|null
     */
    public $objectId;

    /**
     * @var string
     */
    public $transformer = [
        'class' => PopulateElementFromResponse::class,
        'action' => 'sync'
    ];

    /**
     * @param \craft\queue\QueueInterface|\yii\queue\Queue $queue
     * @return bool
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        return $this->syncDown(
            $this->getElement(),
            $this->getField(),
            $this->objectId
        );
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param string $objectId
     * @return bool
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function syncDown(
        ElementInterface $element,
        Objects $field,
        string $objectId = null
    ): bool {

        $id = $objectId ?: $this->resolveObjectIdFromElement($element, $field);
        if (null === $id) {
            return false;
        }

        $response = SObject::read(
            $field->getConnection(),
            $field->getCache(),
            $field->object,
            $id
        );

        if (($response->getStatusCode() < 200 || $response->getStatusCode() > 300)) {
            call_user_func_array(
                new PopulateElementErrorsFromResponse(),
                [
                    $response,
                    $element,
                    $field,
                    $id
                ]
            );
            return false;
        }

        if (null !== ($transformer = $this->resolveTransformer($this->transformer))) {
            call_user_func_array(
                $transformer,
                [
                    $response,
                    $element,
                    $field,
                    $id
                ]
            );
        }

        if ($objectId !== null) {
            $this->addAssociation(
                $element,
                $field,
                $id
            );
        }

        return Craft::$app->getElements()->saveElement($element);
    }

    /**
     * @param ElementInterface|Element $element
     * @param Objects $field
     * @param string $id
     */
    protected function addAssociation(
        ElementInterface $element,
        Objects $field,
        string $id
    ) {
        /** @var IntegrationAssociation $recordClass */
        $recordClass = $field::recordClass();

        /** @var IntegrationAssociationQuery $associations */
        $associationQuery = $element->getFieldValue($field->handle);
        $associations = ArrayHelper::index($associationQuery->all(), 'objectId');

        if (!array_key_exists($id, $associations)) {
            $association = new $recordClass([
                'element' => $element,
                'field' => $field,
                'siteId' => SiteHelper::ensureSiteId($element->siteId),
                'objectId' => $id
            ]);

            $associations = array_merge(
                $associationQuery->all(),
                [
                    $association
                ]
            );

            $associationQuery->setCachedResult(array_values($associations));
        }
    }
}

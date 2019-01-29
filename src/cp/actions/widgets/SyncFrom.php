<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\actions\widgets;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\integration\actions\ResolverTrait;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\salesforce\cp\actions\sync\AbstractSyncFrom;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\Force;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SyncFrom extends AbstractSyncFrom
{
    use ResolverTrait;

    /**
     * @param string $id
     * @param string $field
     * @param string $elementType
     * @param int|null $siteId
     * @return ElementInterface|mixed
     * @throws HttpException
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function run(string $id, string $field, string $elementType, int $siteId = null)
    {
        /** @var Objects $field */
        $field = $this->resolveField($field);

        /** @var ElementInterface $element */
        $element = $this->autoResolveElement($field, $id, $elementType, $siteId);

        return $this->runInternal($element, $field, $id);
    }

    /**
     * @param Integrations $field
     * @param string $id
     * @param string $elementType
     * @param int|null $siteId
     * @return ElementInterface
     */
    private function autoResolveElement(
        Integrations $field,
        string $id,
        string $elementType,
        int $siteId = null
    ): ElementInterface {
        /** @var IntegrationAssociation $recordClass */
        $recordClass = $field::recordClass();

        /** @var IntegrationAssociationQuery $query */
        $query = $recordClass::find();
        $query->select(['elementId'])
            ->fieldId($field->id)
            ->objectId($id)
            ->siteId(SiteHelper::ensureSiteId($siteId));

        if ($elementId = $query->scalar()) {
            try {
                $element = $this->resolveElement($elementId);
            } catch (HttpException $e) {
                Force::warning(sprintf(
                    "Unable to find element '%s' associted to Salesforce object '%s' Id '%s'",
                    $elementId,
                    $field->object,
                    $id
                ));
            }
        }

        if (empty($element)) {
            $element = Craft::$app->getElements()->createElement($elementType);
        }

        return $element;
    }
}

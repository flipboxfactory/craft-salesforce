<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use flipbox\craft\salesforce\criteria\ObjectCriteria;
use flipbox\craft\salesforce\fields\actions\SyncItemFrom;
use flipbox\craft\salesforce\fields\actions\SyncItemTo;
use flipbox\craft\salesforce\fields\actions\SyncTo;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\helpers\ObjectHelper;
use flipbox\craft\salesforce\helpers\TransformerHelper;
use flipbox\craft\salesforce\records\ObjectAssociation;
use flipbox\craft\salesforce\transformers\PopulateElementErrorsFromResponse;
use flipbox\craft\salesforce\transformers\PopulateElementErrorsFromUpsertResponse;
use Flipbox\Salesforce\Connections\ConnectionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Objects extends Integrations
{
    /**
     * The Plugin's translation category
     */
    const TRANSLATION_CATEGORY = 'salesforce';

    /**
     * @inheritdoc
     */
    const INPUT_TEMPLATE_PATH = 'salesforce/_components/fieldtypes/Objects/input';

    /**
     * @inheritdoc
     */
    const INPUT_ITEM_TEMPLATE_PATH = 'salesforce/_components/fieldtypes/Objects/_inputItem';

    /**
     * @inheritdoc
     */
    const SETTINGS_TEMPLATE_PATH = 'salesforce/_components/fieldtypes/Objects/settings';

    /**
     * @inheritdoc
     */
    const ACTION_PREFORM_ACTION_PATH = 'salesforce/cp/fields/perform-action';

    /**
     * @inheritdoc
     */
    const ACTION_CREATE_ITEM_PATH = 'salesforce/cp/fields/create-item';

    /**
     * @inheritdoc
     */
    const ACTION_ASSOCIATION_ITEM_PATH = 'salesforce/cp/objects/associate';

    /**
     * @inheritdoc
     */
    const ACTION_DISSOCIATION_ITEM_PATH = 'salesforce/cp/objects/dissociate';

    /**
     * @inheritdoc
     */
    const ACTION_PREFORM_ITEM_ACTION_PATH = 'salesforce/cp/fields/perform-item-action';

    /**
     * @var string
     */
    public $object;

    /**
     * Indicates whether the full sync operation should be preformed if a matching HubSpot Object was found but not
     * currently associated to the element.  For example, when attempting to Sync a Craft User to a HubSpot Contact, if
     * the HubSpot Contact already exists; true would override data in HubSpot while false would just perform
     * an association (note, a subsequent sync operation could be preformed)
     * @var bool
     *
     * @deprecated
     */
    public $syncOnMatch = false;

    /**
     * @inheritdoc
     */
    protected $defaultAvailableActions = [
        SyncTo::class
    ];

    /**
     * @inheritdoc
     */
    protected $defaultAvailableItemActions = [
        SyncItemFrom::class,
        SyncItemTo::class,
    ];


    /*******************************************
     * OBJECT
     *******************************************/

    /**
     * @return string
     */
    public function getObject(): string
    {
        return (string)$this->object ?: '';
    }

    /**
     * @return string
     */
    public function getObjectLabel(): string
    {
        return StringHelper::titleize($this->object);
    }


    /*******************************************
     * RULES
     *******************************************/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    'object',
                    'required',
                    'message' => Force::t('Object cannot be empty.')
                ],
                [
                    [
                        'object',
                    ],
                    'safe',
                    'on' => [
                        self::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }


    /*******************************************
     * SETTINGS
     *******************************************/

    /**
     * @inheritdoc
     */
    public function settingsAttributes(): array
    {
        return array_merge(
            [
                'object'
            ],
            parent::settingsAttributes()
        );
    }

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return ObjectAssociation::class;
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Force::t('Salesforce Objects');
    }

    /**
     * @inheritdoc
     */
    public static function defaultSelectionLabel(): string
    {
        return Force::t('Add a Salesforce Object');
    }


    /*******************************************
     * CONNECTION
     *******************************************/

    /**
     * @return ConnectionInterface
     * @throws \flipbox\craft\integration\exceptions\ConnectionNotFound
     */
    public function getConnection(): ConnectionInterface
    {
        return Force::getInstance()->getConnections()->get();
    }


    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return Force::getInstance()->getCache()->get();
    }


    /*******************************************
     * SALESFORCE
     *******************************************/

    /**
     * @param string $id
     * @return ResponseInterface
     */
    public function readFromSalesforce(
        string $id
    ): ResponseInterface {

        return (new ObjectCriteria([
            'connection' => $this->getConnection(),
            'cache' => $this->getCache(),
            'object' => $this->object,
            'id' => $id
        ]))->read();
    }


    /**
     * @param array $payload
     * @param string|null $id
     * @return ResponseInterface
     * @throws \flipbox\craft\integration\exceptions\ConnectionNotFound
     */
    protected function upsertToSalesforce(
        array $payload,
        string $id = null
    ) {
        return (new ObjectCriteria([
            'connection' => $this->getConnection(),
            'cache' => $this->getCache(),
            'object' => $this->object,
            'payload' => $payload,
            'id' => $id
        ]))->upsert();
    }


    /*******************************************
     * SYNC TO
     *******************************************/

    /**
     * @param ElementInterface $element
     * @param string|null $objectId
     * @param null $transformer
     * @return ResponseInterface|null
     * @throws \Throwable
     * @throws \flipbox\craft\integration\exceptions\ConnectionNotFound
     */
    public function pushToSalesforce(
        ElementInterface $element,
        string $objectId = null,
        $transformer = null
    ) {
        /** @var Element $element */

        $id = $objectId ?: $this->findObjectIdFromElementId($element->getId());

        // Get callable used to create payload
        if (null === ($transformer = TransformerHelper::resolveTransformer($transformer))) {
            $transformer = Force::getInstance()->getSettings()->getSyncUpsertPayloadTransformer();
        }

        // Create payload
        $payload = call_user_func_array(
            $transformer,
            [
                $element,
                $this,
                $id
            ]
        );

        $response = $this->upsertToSalesforce($payload, $id);

        if (!$this->handleSyncToSalesforceResponse(
            $response,
            $element,
            $id
        )) {
            return null;
        }

        return $response;
    }

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function syncToSalesforce(
        ElementInterface $element,
        string $objectId = null,
        $transformer = null
    ): bool {
        return $this->pushToSalesforce($element, $objectId, $transformer) !== null;
    }


    /*******************************************
     * SYNC FROM
     *******************************************/

    /**
     * @param ElementInterface $element
     * @param string|null $objectId
     * @param null $transformer
     * @return ResponseInterface|null
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function pullFromSalesforce(
        ElementInterface $element,
        string $objectId = null,
        $transformer = null
    ) {

        $id = $objectId ?: $this->findObjectIdFromElementId($element->getId());

        if (null === $id) {
            return null;
        }

        $response = $this->readFromSalesforce($id);

        if (($response->getStatusCode() < 200 || $response->getStatusCode() >= 300)) {
            call_user_func_array(
                new PopulateElementErrorsFromResponse(),
                [
                    $response,
                    $element,
                    $this,
                    $id
                ]
            );
            return null;
        }

        // Get callable used to populate element
        if (null === ($transformer = TransformerHelper::resolveTransformer($transformer))) {
            $transformer = Force::getInstance()->getSettings()->getSyncPopulateElementTransformer();
        }

        // Populate element
        call_user_func_array(
            $transformer,
            [
                $response,
                $element,
                $this,
                &$id
            ]
        );

        if (!Craft::$app->getElements()->saveElement($element)) {
            return null;
        }

        if ($objectId !== null) {
            if (!ObjectHelper::isCaseSafeObjectId($id)) {
                $id = $this->getObjectIdFromResponse($response);
            }

            $this->addAssociation(
                $element,
                $id
            );
        }

        return $response;
    }

    /**
     * @@inheritdoc
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function syncFromSalesforce(
        ElementInterface $element,
        string $objectId = null,
        $transformer = null
    ): bool {
        return $this->pullFromSalesforce($element, $objectId, $transformer) !== null;
    }

    /*******************************************
     * ASSOCIATIONS
     *******************************************/

    /**
     * @param ElementInterface|Element $element
     * @param string $id
     * @return bool
     * @throws \Throwable
     */
    public function addAssociation(
        ElementInterface $element,
        string $id
    ) {
        /** @var IntegrationAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($this->handle))) {
            Force::warning("Field is not available on element.");
            return false;
        };

        $associations = ArrayHelper::index($query->all(), 'objectId');

        if (!array_key_exists($id, $associations)) {
            $associations[$id] = $association = new ObjectAssociation([
                'element' => $element,
                'field' => $this,
                'siteId' => SiteHelper::ensureSiteId($element->siteId),
                'objectId' => $id
            ]);

            $query->setCachedResult(array_values($associations));

            return $association->save();
        }

        return true;
    }

    /**
     * @param ElementInterface|Element $element
     * @param string $id
     * @return bool
     * @throws \Throwable
     */
    public function removeAssociation(
        ElementInterface $element,
        string $id
    ) {
        /** @var IntegrationAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($this->handle))) {
            Force::warning("Field is not available on element.");
            return false;
        };

        /** @var ObjectAssociation[] $association */
        $associations = ArrayHelper::index($query->all(), 'objectId');

        if ($association = ArrayHelper::remove($associations, $id)) {
            $query->clearCachedResult();
            return $association->delete();
        }

        return true;
    }

    /**
     * @param ResponseInterface|Element $response
     * @param ElementInterface $element
     * @param string|null $objectId
     * @return bool
     * @throws \Throwable
     */
    protected function handleSyncToSalesforceResponse(
        ResponseInterface $response,
        ElementInterface $element,
        string $objectId = null
    ): bool {

        if (!($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299)) {
            call_user_func_array(
                new PopulateElementErrorsFromUpsertResponse(),
                [
                    $response,
                    $element,
                    $this,
                    $objectId
                ]
            );
            return false;
        }

        if (empty($objectId)) {
            if (null === ($objectId = $this->getObjectIdFromResponse($response))) {
                Force::error("Unable to determine object id from response");
                return false;
            };

            return $this->addAssociation($element, $objectId);
        }

        return true;
    }

    /**
     * @param ResponseInterface $response
     * @return string|null
     */
    protected function getObjectIdFromResponse(ResponseInterface $response)
    {
        $response->getBody()->rewind();

        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        $id = $data['Id'] ?? ($data['id'] ?? null);

        return $id ? (string)$id : null;
    }

    /**
     * @param int $elementId
     * @param int|null $siteId
     * @return bool|false|string|null
     */
    public function findObjectIdFromElementId(
        int $elementId = null,
        int $siteId = null
    ) {
        if ($elementId === null) {
            return null;
        }

        if (!$objectId = ObjectAssociation::find()
            ->select(['objectId'])
            ->elementId($elementId)
            ->fieldId($this->id)
            ->siteId(SiteHelper::ensureSiteId($siteId))
            ->scalar()
        ) {
            Force::warning(sprintf(
                "Salesforce Object Id association was not found for element '%s'",
                $elementId
            ));

            return null;
        }

        Force::info(sprintf(
            "Salesforce Object Id '%s' was found for element '%s'",
            $objectId,
            $elementId
        ));

        return $objectId;
    }

    /**
     * @param string $objectId
     * @param int|null $siteId
     * @return bool|false|string|null
     */
    public function findElementIdFromObjectId(
        string $objectId = null,
        int $siteId = null
    ) {
        if ($objectId === null) {
            return null;
        }

        if (!$elementId = ObjectAssociation::find()
            ->select(['elementId'])
            ->objectId($objectId)
            ->fieldId($this->id)
            ->siteId(SiteHelper::ensureSiteId($siteId))
            ->scalar()
        ) {
            Force::warning(sprintf(
                "Element Id association was not found for Salesforce object '%s'",
                $objectId
            ));

            return null;
        }

        Force::info(sprintf(
            "Element Id '%s' was found for Salesforce object '%s'",
            $elementId,
            $objectId
        ));

        return $elementId;
    }
}

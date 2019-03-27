<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers\view;

use Craft;
use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;
use flipbox\craft\salesforce\fields\Objects;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\helpers\TransformerHelper;
use flipbox\craft\salesforce\transformers\CreateUpsertPayloadFromElement;
use yii\web\HttpException;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectPayloadsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . '/objects';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . '/payload';

    /**
     * @param int $field
     * @param int $element
     * @return Response
     * @throws HttpException
     */
    public function actionIndex(int $field, int $element): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        /** @var Objects $field */
        $field = Craft::$app->getFields()->getFieldById($field);
        if (!$field instanceof ObjectsFieldInterface) {
            throw new HttpException(401, "Invalid field.");
        }
        $variables['field'] = $field;

        $element = Craft::$app->getElements()->getElementById($element);
        if (!$element instanceof ElementInterface) {
            throw new HttpException(401, "Invalid element.");
        }
        $variables['element'] = $element;

        $variables['tabs'] = $this->getTabs();

        $payloads = [];

        foreach ($this->getPayloadTypes() as $type) {
            $payloads[$type] = $this->getPayload(
                $field,
                $element,
                $type
            );
        }

        $variables['payloads'] = $payloads;

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @return array
     */
    private function getPayloadTypes(): array
    {
        return [
            TransformerHelper::PAYLOAD_ACTION_SYNC,
            TransformerHelper::PAYLOAD_ACTION_SAVE
        ];
    }

    /**
     * @param Objects $field
     * @param ElementInterface $element
     * @param string $action
     * @return array
     */
    protected function getPayload(
        Objects $field,
        ElementInterface $element,
        string $action
    ): array {

        $transformer = [
            'class' => CreateUpsertPayloadFromElement::class,
            'action' => $action
        ];

        // Get callable used to create payload
        if (null === ($transformer = TransformerHelper::resolveTransformer($transformer))) {
            return [];
        }

        // Create payload
        $payload = call_user_func_array(
            $transformer,
            [
                $element,
                $field
            ]
        );

        return $payload;
    }


    /**
     * @return array
     */
    private function getTabs(): array
    {
        $return = [];

        foreach ($this->getPayloadTypes() as $type) {
            $return[$type] = [
                'label' => Force::t(StringHelper::toTitleCase($type)),
                'url' => '#' . $type
            ];
        }

        return $return;
    }

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/object-fields';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/object-fields';
    }


    /*******************************************
     * VARIABLES
     *******************************************/

    /**
     * @inheritdoc
     */
    protected function baseVariables(array &$variables = [])
    {
        parent::baseVariables($variables);

        $title = Force::t("Object Payload");
        $variables['title'] .= ' ' . $title;

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $title,
            'url' => ''
        ];
    }
}

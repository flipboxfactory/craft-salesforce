<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\actions\sync;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\ember\actions\CheckAccessTrait;
use flipbox\craft\salesforce\fields\Objects;
use yii\base\Action;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractSyncTo extends Action
{
    use CheckAccessTrait;

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return ElementInterface|mixed
     * @throws \Throwable
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        ElementInterface $element,
        Objects $field
    ) {
        // Check access
        if (($access = $this->checkAccess($element, $field)) !== true) {
            return $access;
        }

        if (false === $this->performAction($element, $field)) {
            return $this->handleFailResponse($element);
        }

        return $this->handleSuccessResponse($element);
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return bool
     * @throws \Throwable
     */
    protected function performAction(
        ElementInterface $element,
        Objects $field
    ) {
        return $field->syncToSalesforce(
            $element
        );
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleSuccessResponse(ElementInterface $element)
    {
        // Success status code
        Craft::$app->getResponse()->setStatusCode($element ? 200 : 201);
        return $element;
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleFailResponse(ElementInterface $element)
    {
        Craft::$app->getResponse()->setStatusCode(400);
        return $element;
    }
}

<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers\view;

use Craft;
use craft\helpers\UrlHelper;
use flipbox\craft\ember\helpers\ArrayHelper;
use flipbox\craft\salesforce\criteria\InstanceCriteria;
use flipbox\craft\salesforce\criteria\ObjectCriteria;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\transformers\DynamicModelResponse;
use yii\base\DynamicModel;
use yii\base\UnknownPropertyException;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . '/objects';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . '/index';

    /**
     * @return Response
     * @throws \Exception
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $variables['describedObject'] = null;

        if ($object = Craft::$app->getRequest()->getParam('object')) {
            $criteria = new ObjectCriteria([
                'object' => $object
            ]);

            if (null !== ($connection = $this->findActiveConnection())) {
                $criteria->setConnection($connection->handle);

                $model = call_user_func_array(
                    new DynamicModelResponse(),
                    [
                        $criteria->describe()
                    ]
                );
            }

            $variables['describedObject'] = $model ?? $this->invalidConnectionModel();
        }

        $variables['objectOptions'] = $this->getObjectOptions();
        $variables['tabs'] = $this->getTabs();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @return array
     */
    private function getObjectOptions()
    {
        $describeOptions = [];

        try {
            if (null !== ($connection = $this->findActiveConnection())) {
                $criteria = new InstanceCriteria([
                    'connection' => $connection->handle
                ]);

                /** @var DynamicModel $model */
                $model = call_user_func_array(
                    new DynamicModelResponse(),
                    [
                        $criteria->describe()
                    ]
                );

                foreach (ArrayHelper::getValue($model, 'sobjects', []) as $object) {
                    $describeOptions[] = [
                        'label' => $object['label'],
                        'value' => $object['name']
                    ];
                }
            }

            // Sort them by name
            ArrayHelper::multisort($describeOptions, 'label');

        } catch (UnknownPropertyException $e) {
            // intentionally doing nothing
        }

        return [
                [
                    'label' => 'Select Salesforce Object'
                ]
            ] + $describeOptions;
    }

    /**
     * @return array
     */
    private function getTabs(): array
    {
        return [
            'fields' => [
                'label' => Force::t('Fields'),
                'url' => '#fields'
            ],
            'relations' => [
                'label' => Force::t('Relationships'),
                'url' => '#relations'
            ]
        ];
    }

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/objects';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/objects';
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

        $title = Force::t("Objects");
        $variables['title'] .= ' ' . $title;

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $title,
            'url' => UrlHelper::url($this->getBaseCpPath())
        ];
    }
}

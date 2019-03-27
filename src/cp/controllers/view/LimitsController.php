<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers\view;

use craft\helpers\UrlHelper;
use flipbox\craft\salesforce\criteria\InstanceCriteria;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\transformers\DynamicModelResponse;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class LimitsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . '/limits';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . '/index';

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        if (null !== ($connection = $this->findActiveConnection())) {
            $criteria = new InstanceCriteria([
                'connection' => $connection->getConnection()
            ]);

            $model = call_user_func_array(
                new DynamicModelResponse(),
                [
                    $criteria->limits()
                ]
            );
        }

        $variables['limits'] = $model ?? $this->invalidConnectionModel();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/limits';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/limits';
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

        $title = Force::t("Limits");
        $variables['title'] .= ' ' . $title;

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $title,
            'url' => UrlHelper::url($this->getBaseCpPath())
        ];
    }
}

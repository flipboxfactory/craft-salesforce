<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\controllers;

use craft\helpers\ArrayHelper;
use flipbox\craft\ember\controllers\AbstractController;
use flipbox\craft\salesforce\actions\webhooks\Process;
use flipbox\craft\salesforce\filters\WebhookAuth;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
class WebhooksController extends AbstractController
{

    /**
     * @inheritdoc
     */
    protected $allowAnonymous = ['process'];

    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => WebhookAuth::class,
                    'except' => [
                        'options',
                        'head'
                    ]
                ]
            ]
        );
    }

    /**
     * @return array
     */
    public function actions()
    {
        return ArrayHelper::merge(
            parent::actions(),
            [
                'process' => [
                    'class' => Process::class
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function verbs(): array
    {
        return array_merge(
            parent::verbs(),
            [
                'process' => ['POST', 'PATCH', 'PUT']
            ]
        );
    }
}

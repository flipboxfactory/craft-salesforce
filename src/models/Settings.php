<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\models;

use Craft;
use craft\base\Model;
use craft\helpers\StringHelper;
use flipbox\craft\ember\helpers\ModelHelper;
use flipbox\craft\salesforce\helpers\TransformerHelper;
use flipbox\craft\salesforce\services\Cache;
use flipbox\craft\salesforce\transformers\CreateUpsertPayloadFromElement;
use flipbox\craft\salesforce\transformers\PopulateElementFromResponse;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Settings extends Model
{
    const DEFAULT_CONNECTION = 'salesforce';

    /**
     * @var string
     */
    public $environmentTablePostfix = '';

    /**
     * @var string
     */
    private $defaultCache = Cache::APP_CACHE;

    /**
     * @var string
     */
    private $defaultConnection = self::DEFAULT_CONNECTION;

    /**
     * @var string
     */
    private $webhookToken;

    /**
     * @param string $key
     * @return $this
     */
    public function setDefaultConnection(string $key)
    {
        $this->defaultConnection = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultConnection(): string
    {
        return $this->defaultConnection;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setDefaultCache(string $key)
    {
        $this->defaultCache = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultCache(): string
    {
        return $this->defaultCache;
    }

    /**
     * @return callable
     */
    public function getSyncUpsertPayloadTransformer(): callable
    {
        return TransformerHelper::resolveTransformer([
            'class' => CreateUpsertPayloadFromElement::class,
            'action' => TransformerHelper::PAYLOAD_ACTION_SYNC
        ]);
    }

    /**
     * @return callable
     */
    public function getSyncPopulateElementTransformer(): callable
    {
        return TransformerHelper::resolveTransformer([
            'class' => PopulateElementFromResponse::class,
            'action' => TransformerHelper::PAYLOAD_ACTION_SYNC
        ]);
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setWebHookToken(string $token)
    {
        $this->webhookToken = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebHookToken(): string
    {
        return $this->webhookToken ?: StringHelper::randomString();
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            [
                'defaultConnection',
                'defaultCache',
                'webHookToken'
            ]
        );
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'defaultConnection',
                        'defaultCache',
                        'webHookToken'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}

<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\actions\webhooks;

use Craft;
use flipbox\craft\ember\actions\ManageTrait;
use flipbox\craft\salesforce\events\ReceiveWebhookEvent;
use yii\base\Action;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Process extends Action
{
    use ManageTrait;

    const EVENT_RECEIVE_WEBHOOK = 'receiveWebhook';

    /**
     * @inheritdoc
     */
    public $statusCodeSuccess = 201;

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\HttpException
     */
    public function run()
    {
        return $this->runInternal(
            Craft::$app->getRequest()->getBodyParams()
        );
    }

    /**
     * @inheritdoc
     */
    protected function performAction(array $data = []): bool
    {
        if (!$data) {
            $this->handleFailResponse("No data.");
        }

        if ($this->hasEventHandlers(self::EVENT_RECEIVE_WEBHOOK)) {
            $this->trigger(
                self::EVENT_RECEIVE_WEBHOOK,
                new ReceiveWebhookEvent([
                    'data' => $data
                ])
            );
        }

        $this->handleSuccessResponse(null);
    }
}

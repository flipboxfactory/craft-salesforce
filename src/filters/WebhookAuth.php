<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\filters;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */

use Craft;
use craft\helpers\Json;
use flipbox\craft\salesforce\Force;
use yii\filters\auth\AuthMethod;

class WebhookAuth extends AuthMethod
{
    /**
     * @var string A "realm" attribute MAY be included to indicate the scope
     * of protection in the manner described in HTTP/1.1 [RFC2617].  The "realm"
     * attribute MUST NOT appear more than once.
     */
    public $realm = 'api';

    /**
     * @var string Authorization header schema, default 'Bearer'
     */
    public $schema = 'Bearer';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader === null || preg_match('/^' . $this->schema . '\s+(.*?)$/', $authHeader, $matches) === false) {
            return null;
        }

        // Header does not match schema
        if (empty($matches)) {
            return null;
        }

        // Header schema is a match, but no token or invalid token
        if (!isset($matches[1]) || $matches[1] !== Force::getInstance()->getSettings()->getWebHookToken()) {
            Force::error(
                sprintf(
                    "Unauthorized: [%s]",
                    Json::encode([
                        'IP Address' => Craft::$app->getRequest()->getUserIP(),
                        'Operating System' => Craft::$app->getRequest()->getClientOs(),
                        'Token' => $matches[1]
                    ])
                ),
                'webook',
                true
            );
            $this->handleFailure($response);
        }

        return true;
    }
}

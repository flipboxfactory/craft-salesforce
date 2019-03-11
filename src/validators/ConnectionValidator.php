<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\validators;

use flipbox\craft\salesforce\connections\SavableConnectionInterface;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\records\Connection;
use Flipbox\Salesforce\Connections\ConnectionInterface;
use yii\base\Model;
use yii\validators\Validator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ConnectionValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $class = $model->$attribute;

        // Handles are always required, so if it's blank, the required validator will catch this.
        if ($class) {
            if (!$this->isValid($class)) {
                $message = Force::t(
                    '“{class}” is a not a valid connection.',
                    ['class' => $class]
                );
                $this->addError($model, $attribute, $message);
            }

            $this->validateConnection($model, $class, $attribute);
        }
    }

    /**
     * @param string $class
     * @return bool
     */
    protected function isValid(string $class): bool
    {
        return $class instanceof ConnectionInterface ||
            is_subclass_of($class, ConnectionInterface::class);
    }

    /**
     * @param Model $model
     * @param string $class
     * @param $attribute
     */
    protected function validateConnection(Model $model, string $class, $attribute)
    {
        if (!$model instanceof Connection) {
            return;
        }

        /** @var ConnectionInterface $connection */
        $connection = $model->getConnection();

        if (!$connection instanceof SavableConnectionInterface) {
            return;
        }

        if (!$connection->validate()) {
            $message = Force::t(
                'Invalid settings.',
                ['class' => $class]
            );
            $this->addError($model, $attribute, $message);
        }
    }
}

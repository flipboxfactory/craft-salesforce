<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\web\twig;

use Craft;
use flipbox\craft\salesforce\web\twig\variables\Force;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
class Extension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getGlobals(): array
    {
        return [
            'salesforce' => Craft::createObject(Force::class)
        ];
    }
}

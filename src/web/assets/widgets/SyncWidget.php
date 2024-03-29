<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\web\assets\widgets;

use craft\web\AssetBundle;
use flipbox\craft\ember\web\assets\actions\Actions;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SyncWidget extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->js = [
            'js/Sync.min.js'
        ];
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/dist';

    /**
     * @inheritdoc
     */
    public $depends = [
        Actions::class
    ];
}

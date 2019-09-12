<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\events;

use Craft;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
class DownloadDocumentEvent extends Event
{
    /**
     * The 'Content Version' Salesforce Object which contained the file.
     *
     * Ref: https://developer.salesforce.com/docs/atlas.en-us.object_reference.meta/object_reference/sforce_api_objects_contentversion.htm
     * @var array
     */
    public $object;

    /**
     * The file name.
     *
     * @var string
     */
    public $fileName;

    /**
     * The raw file contents.
     *
     * @var string
     */
    public $fileContents;

    /**
     * @var bool whether the document can be downloaded. Defaults to true.
     */
    public $canDownload;

    /**
     * @var bool whether the document can be downloaded. Defaults to true.
     */
    public $unauthorizedMessage = "Unauthorized";

    /**
     * Allow ONLY admins to download everything by default
     */
    public function init()
    {
        if (null === $this->canDownload) {
            $this->canDownload = Craft::$app->getUser()->getIsAdmin();
        }

        parent::init();
    }
}
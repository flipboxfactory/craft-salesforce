<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\controllers;

use Craft;
use craft\helpers\Json;
use flipbox\craft\ember\controllers\AbstractController;
use flipbox\craft\salesforce\criteria\ObjectCriteria;
use flipbox\craft\salesforce\criteria\UrlCriteria;
use flipbox\craft\salesforce\events\DownloadAttachmentEvent;
use flipbox\craft\salesforce\Force;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
class AttachmentsController extends AbstractController
{
    const EVENT_DOWNLOAD_ATTACHMENT = 'downloadAttachment';

    /**
     * @param string|null $id
     * @return \craft\web\Response|\yii\console\Response
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\HttpException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionDownload(string $id = null, bool $canDownload = null, bool $inline = false)
    {
        if ($canDownload !== true || !Force::getInstance()->getSettings()->enableDocumentDownloads) {
            throw new UnauthorizedHttpException("Unable to download attachment.");
        }

        $id = $id ?? Craft::$app->getRequest()->getRequiredBodyParam('id');
        $inline = (bool) ($inline ?? Craft::$app->getRequest()->getParam('inline', $inline));

        $result = (new ObjectCriteria())
            ->setObject('Attachment')
            ->setId($id)
            ->read();

        if ($result->getStatusCode() !== 200) {
            throw new NotFoundHttpException(
                Craft::t(
                    'salesforce',
                    'The document could not be found.'
                )
            );
        }

        $object = Json::decodeIfJson(
            $result->getBody()->getContents()
        );

        $result = (new UrlCriteria())
            ->setUrl($object['Body'])
            ->read();

        if ($result->getStatusCode() !== 200) {
            throw new NotFoundHttpException(
                Craft::t(
                    'salesforce',
                    'The document contents could not be found.'
                )
            );
        }

        $event = new DownloadAttachmentEvent([
            'object' => $object,
            'fileName' => $object['Name'],
            'fileContents' => $result->getBody()->getContents(),
            'canDownload' => $canDownload
        ]);

        $this->trigger(
            self::EVENT_DOWNLOAD_ATTACHMENT,
            $event
        );

        if (!$event->canDownload) {
            throw new UnauthorizedHttpException(
                Craft::t(
                    'salesforce',
                    $event->unauthorizedMessage
                )
            );
        }

        return Craft::$app->getResponse()->sendContentAsFile(
            $event->fileContents,
            $event->fileName,
            [
                'inline' => $inline
            ]
        );
    }
}

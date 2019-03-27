<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\cp\controllers\view;

use Craft;
use craft\web\Controller;
use flipbox\craft\ember\helpers\UrlHelper;
use flipbox\craft\salesforce\cp\Cp as CpModule;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\records\Connection;
use yii\base\DynamicModel;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property CpModule $module
 */
abstract class AbstractController extends Controller
{
    /**
     * The index view template path
     */
    const TEMPLATE_BASE = 'salesforce' . DIRECTORY_SEPARATOR . '_cp';

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return Force::getInstance()->getUniqueId() . '/cp';
    }

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return Force::getInstance()->getUniqueId();
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function getBaseContinueEditingUrl(string $endpoint = ''): string
    {
        return $this->getBaseCpPath() . $endpoint;
    }


    /*******************************************
     * CONNECTIONS
     *******************************************/

    /**
     * @return array
     */
    protected function getConnections(): array
    {
        return Connection::findAll(['enabled' => true]);
    }

    /**
     * @return Connection|null
     */
    protected function findDefaultConnection()
    {
        return Connection::findOne([
            'enabled' => true,
            'handle' => Force::getInstance()->getSettings()->getDefaultConnection()
        ]);
    }

    /**
     * @return Connection|null
     */
    protected function findActiveConnection()
    {
        $selectedConnection = Craft::$app->getRequest()->getParam(
            'connection',
            Force::getInstance()->getSettings()->getDefaultConnection()
        );

        $connection = Connection::findOne([
            'enabled' => true,
            'handle' => $selectedConnection
        ]) ?: $this->findDefaultConnection();

        if ($connection === null) {
            $connections = $this->getConnections();

            if(count($connections) === 1) {
                return reset($connections);
            }
        }

        return $connection;
    }

    /**
     * @return DynamicModel
     */
    protected function invalidConnectionModel(): DynamicModel
    {
        $model = new DynamicModel();
        $model->addError(
            'connection',
            'Invalid connection. ' .
            '<a href="' . UrlHelper::cpUrl('salesforce/settings/connections') . '">' .
            'Manage connections to Salesforce' .
            '</a>.'
        );

        return $model;
    }

    /*******************************************
     * VARIABLES
     *******************************************/

    /**
     * @inheritdoc
     */
    protected function baseVariables(array &$variables = [])
    {
        $module = Force::getInstance();

        $title = Force::t("Salesforce");

        // Settings
        $variables['settings'] = $module->getSettings();
        $variables['title'] = $title;

        // Connections
        $variables['availableConnections'] = $this->getConnections();
        $variables['defaultConnection'] = $this->findDefaultConnection();
        $variables['activeConnection'] = $this->findActiveConnection();

        // Path to controller actions
        $variables['baseActionPath'] = $this->getBaseActionPath();

        // Path to CP
        $variables['baseCpPath'] = $this->getBaseCpPath();

        // Set the "Continue Editing" URL
        $variables['continueEditingUrl'] = $this->getBaseCpPath();

        // Select our sub-nav
        if (!$activeSubNav = Craft::$app->getRequest()->getSegment(2)) {
            $activeSubNav = 'queries';
        }
        $variables['selectedSubnavItem'] = 'salesforce.' . $activeSubNav;

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $title,
            'url' => UrlHelper::url(Force::getInstance()->getUniqueId())
        ];
    }

    /*******************************************
     * UPSERT VARIABLES
     *******************************************/

    /**
     * @param array $variables
     */
    protected function insertVariables(array &$variables)
    {
        // apply base view variables
        $this->baseVariables($variables);

        // Set the "Continue Editing" URL
        $variables['continueEditingUrl'] = $this->getBaseContinueEditingUrl('/{id}');

        // Append title
        $variables['title'] .= ' - ' . Force::t('New');
    }
}

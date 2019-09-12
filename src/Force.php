<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Dashboard;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use flipbox\craft\ember\helpers\UrlHelper;
use flipbox\craft\ember\modules\LoggerTrait;
use flipbox\craft\psr3\Logger;
use flipbox\craft\salesforce\fields\Objects as ObjectsField;
use flipbox\craft\salesforce\models\Settings as SettingsModel;
use flipbox\craft\salesforce\web\twig\Extension;
use flipbox\craft\salesforce\web\twig\variables\Force as ForceVariable;
use Flipbox\Salesforce\Salesforce;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method SettingsModel getSettings()
 *
 * @property services\Cache $cache
 * @property services\Connections $connections
 * @property Logger $psr3Logger
 */
class Force extends Plugin
{
    use LoggerTrait;

    /**
     * @var string
     */
    public static $category = 'salesforce';

    /**
     * @inheritdocfind
     */
    public function init()
    {
        parent::init();

        // Components
        $this->setComponents([
            'cache' => services\Cache::class,
            'connections' => services\Connections::class,
            'psr3Logger' => function () {
                return Craft::createObject([
                    'class' => Logger::class,
                    'logger' => static::getLogger(),
                    'category' => self::getLogFileName()
                ]);
            }
        ]);

        // Pass logger along to package
        Salesforce::setLogger(
            static::getPsrLogger()
        );

        // Modules
        $this->setModules([
            'cp' => cp\Cp::class
        ]);

        // Add our custom twig extension
        Craft::$app->getView()->getTwig()->addExtension(new Extension());

        // Fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ObjectsField::class;
            }
        );

        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = widgets\ObjectWidget::class;
            }
        );

        // Template variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('salesforce', ForceVariable::class);
            }
        );

        // Integration template directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $e) {
                $e->roots['flipbox/integration'] = Craft::$app->getPath()->getVendorPath() .
                    '/flipboxfactory/craft-integration/src/templates';
            }
        );

        // CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            [self::class, 'onRegisterCpUrlRules']
        );
    }

    /**
     * @return string
     */
    protected static function getLogFileName(): string
    {
        return 'salesforce';
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem()
    {
        return array_merge(
            parent::getCpNavItem(),
            [
                'subnav' => [
                    'salesforce.queries' => [
                        'label' => static::t('Queries'),
                        'url' => 'salesforce/queries'
                    ],
                    'salesforce.objects' => [
                        'label' => static::t('Objects'),
                        'url' => 'salesforce/objects'
                    ],
                    'salesforce.limits' => [
                        'label' => static::t('Limits'),
                        'url' => 'salesforce/limits'
                    ],
                    'salesforce.settings' => [
                        'label' => static::t('Settings'),
                        'url' => 'salesforce/settings',
                    ]
                ]
            ]
        );
    }

    /*******************************************
     * SETTINGS
     *******************************************/

    /**
     * @inheritdoc
     * @return SettingsModel
     */
    public function createSettingsModel()
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     * @throws \yii\base\ExitException
     */
    public function getSettingsResponse()
    {
        Craft::$app->getResponse()->redirect(
            UrlHelper::cpUrl('salesforce/settings')
        );

        Craft::$app->end();
    }


    /*******************************************
     * SERVICES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Cache
     */
    public function getCache(): services\Cache
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('cache');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Connections
     */
    public function getConnections(): services\Connections
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('connections');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return Logger
     */
    public function getPsrLogger(): Logger
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('psr3Logger');
    }


    /*******************************************
     * MODULES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return cp\Cp
     */
    public function getCp(): cp\Cp
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getModule('cp');
    }


    /*******************************************
     * TRANSLATE
     *******************************************/

    /**
     * Translates a message to the specified language.
     *
     * This is a shortcut method of [[\Craft::t()]].
     *     *
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     * @return string the translated message.
     */
    public static function t($message, $params = [], $language = null)
    {
        return Craft::t('salesforce', $message, $params, $language);
    }


    /*******************************************
     * EVENTS
     *******************************************/

    /**
     * @param RegisterUrlRulesEvent $event
     */
    public static function onRegisterCpUrlRules(RegisterUrlRulesEvent $event)
    {
        $event->rules = array_merge(
            $event->rules,
            [
                // ??
                'salesforce' => 'salesforce/cp/view/queries/index',

                // QUERIES
                'salesforce/queries' => 'salesforce/cp/view/queries/index',
                'salesforce/queries/new' => 'salesforce/cp/view/queries/upsert',
                'salesforce/queries/<identifier:\d+>' => 'salesforce/cp/view/queries/upsert',

                // LIMITS
                'salesforce/limits' => 'salesforce/cp/view/limits/index',

                // SOBJECTS
                'salesforce/objects' => 'salesforce/cp/view/objects/index',
                'salesforce/objects/payloads/<field:\d+>/element/<element:\d+>' =>
                    'salesforce/cp/view/object-payloads/index',

                // SETTINGS
                'salesforce/settings' => 'salesforce/cp/settings/view/general/index',

                // SETTINGS: CONNECTIONS
                'salesforce/settings/connections' => 'salesforce/cp/settings/view/connections/index',
                'salesforce/settings/connections/new' => 'salesforce/cp/settings/view/connections/upsert',
                'salesforce/settings/connections/<identifier:\d+>' => 'salesforce/cp/settings/view/connections/upsert',
            ]
        );
    }
}

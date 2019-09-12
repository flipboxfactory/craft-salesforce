<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\salesforce\web\twig\variables;

use flipbox\craft\ember\helpers\QueryHelper;
use flipbox\craft\integration\queries\IntegrationConnectionQuery;
use flipbox\craft\salesforce\criteria\ObjectCriteria;
use flipbox\craft\salesforce\Force as ForcePlugin;
use flipbox\craft\salesforce\helpers\TransformerHelper;
use flipbox\craft\salesforce\models\Settings;
use flipbox\craft\salesforce\queries\SOQLQuery;
use flipbox\craft\salesforce\records\Connection;
use flipbox\craft\salesforce\records\SOQL;
use flipbox\craft\salesforce\services\Cache;
use Psr\Http\Message\ResponseInterface;
use yii\base\DynamicModel;
use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Force extends ServiceLocator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            'cache' => ForcePlugin::getInstance()->getCache(),
            'criteria' => Criteria::class
        ]);
    }

    /**
     * @param array|string $criteria
     * @return SOQLQuery
     */
    public function getQuery($criteria = []): SOQLQuery
    {
        if (is_string($criteria)) {
            $criteria = [(is_numeric($criteria) ? 'id' : 'handle') => $criteria];
        }

        $query = SOQL::find();

        QueryHelper::configure(
            $query,
            $criteria
        );

        return $query;
    }

    /**
     * @param ResponseInterface $response
     * @return DynamicModel
     */
    public function transform(ResponseInterface $response): DynamicModel
    {
        return TransformerHelper::responseToModel($response);
    }

    /**
     * @param array $criteria
     * @return ObjectCriteria
     */
    public function getObject(array $criteria = []): ObjectCriteria
    {
        return new ObjectCriteria($criteria);
    }

    /**
     * Sub-Variables that are accessed 'craft.salesforce.settings'
     *
     * @return Settings
     */
    public function getSettings()
    {
        return ForcePlugin::getInstance()->getSettings();
    }


    /**
     * @param array $config
     * @return IntegrationConnectionQuery
     */
    public function getConnections(array $config = []): IntegrationConnectionQuery
    {
        $query = Connection::find();

        QueryHelper::configure(
            $query,
            $config
        );

        return $query;
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return Cache
     */
    public function getCache(): Cache
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('cache');
    }
}

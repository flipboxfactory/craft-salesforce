<?php

namespace flipbox\craft\salesforce\tests;

use Codeception\Test\Unit;
use flipbox\craft\psr3\Logger;
use flipbox\craft\salesforce\cp\Cp;
use flipbox\craft\salesforce\Force;
use flipbox\craft\salesforce\services\Connections;
use flipbox\craft\salesforce\services\Cache;

class SalesforceTest extends Unit
{
    /**
     * Test the 'Cache' component is set correctly
     */
    public function testCacheComponent()
    {
        $this->assertInstanceOf(
            Cache::class,
            Force::getInstance()->getCache()
        );

        $this->assertInstanceOf(
            Cache::class,
            Force::getInstance()->cache
        );
    }

    /**
     * Test the 'Connections' component is set correctly
     */
    public function testConnectionsComponent()
    {
        $this->assertInstanceOf(
            Connections::class,
            Force::getInstance()->getConnections()
        );

        $this->assertInstanceOf(
            Connections::class,
            Force::getInstance()->connections
        );
    }

    /**
     * Test the 'Logger' component is set correctly
     */
    public function testPSR3Component()
    {
        $this->assertInstanceOf(
            Logger::class,
            Force::getInstance()->getPsrLogger()
        );

        $this->assertInstanceOf(
            Logger::class,
            Force::getInstance()->psr3Logger
        );
    }

    /**
     * Test the 'CP' module is set correctly
     */
    public function testCpModule()
    {
        $this->assertInstanceOf(
            Cp::class,
            Force::getInstance()->getCp()
        );
    }
}

<?php

namespace UserModuleTest;

use Laminas\Mvc\ApplicationInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AbstractLoansWarehouseApiTestCase
 * @package LoansWarehouseTest\LoansWarehouseApi
 */
class AbstractApplicationTestCase extends AbstractHttpControllerTestCase
{
    /** @var ApplicationInterface mvcApp */
    protected ApplicationInterface $mvcApp;

    /** @var ServiceLocatorInterface $container */
    protected ServiceLocatorInterface $container;

    /**  */
    public function setUp(): void
    {
        $this->setApplicationConfig(
            ArrayUtils::merge(
                include __DIR__ . "/_fixtures/config/application.config.php",
                include __DIR__ . "/../config/module.config.php"
            )
        );

        parent::setUp();

        $this->mvcApp = $this->getApplication();
        $this->container = $this->mvcApp->getServiceManager();
    }
}

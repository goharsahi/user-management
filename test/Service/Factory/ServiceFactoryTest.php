<?php

namespace UserModuleTest\Service\Factory;

use Laminas\ApiTools\OAuth2\Provider\UserId\AuthenticationService;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use UserModuleTest\AbstractApplicationTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Service\GoogleClientService;
use UserModule\Service\LoginService;
use UserModule\Service\OauthDbService;

/**
 * Class ProcessFactoryTest
 * @package UserModuleTest\Process\Factory
 */
class ServiceFactoryTest extends AbstractApplicationTestCase
{
    /** */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @dataProvider dataProviderForServiceClasses
     * @param string $serviceClassName
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testFactoryCreatesService(string $serviceClassName)
    {
        $this->assertInstanceOf(
            $serviceClassName,
            $this->container->get("ServiceManager")
                ->get($serviceClassName)
        );
    }

    /**
     * @return array
     */
    public function dataProviderForServiceClasses()
    {
        return [
            [AuthenticationService::class],
            [GoogleClientService::class],
            [LoginService::class],
            [OauthDbService::class],
        ];
    }
}
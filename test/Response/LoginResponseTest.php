<?php

namespace UserModuleTest\Response;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Response\LoginResponse;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class LoginResponseTest
 * @package UserModuleTest\Response
 */
class LoginResponseTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var LoginResponse $loginResponse */
    protected $loginResponse;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loginResponse = $this->container->get("ServiceManager")
            ->get(LoginResponse::class);
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettersAndGetters()
    {
        $this->loginResponse->setResponse([self::DEFAULT_TESTING_VALUE => self::DEFAULT_TESTING_VALUE]);
        $this->assertSame(
            [self::DEFAULT_TESTING_VALUE => self::DEFAULT_TESTING_VALUE],
            $this->loginResponse->getResponse()
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testPopulateReturnsBoolean()
    {
        $this->assertIsBool($this->loginResponse->populate());
    }
}

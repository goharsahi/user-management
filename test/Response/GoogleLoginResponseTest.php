<?php

namespace UserModuleTest\Response;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use UserModule\Response\GoogleLoginResponse;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class GoogleLoginResponseTest
 * @package UserModuleTest\Response
 */
class GoogleLoginResponseTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var GoogleLoginResponse $loginResponse */
    protected $loginResponse;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loginResponse = $this->container->get("ServiceManager")
            ->get(GoogleLoginResponse::class);
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    public function testPopulateReturnsBoolean()
    {
        $this->assertIsBool($this->loginResponse->populate());
    }
}

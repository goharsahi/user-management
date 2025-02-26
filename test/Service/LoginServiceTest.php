<?php

namespace UserModuleTest\Service;

use Laminas\Config\Config;
use Laminas\Session\Container;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\CannotUseOnlyMethodsException;
use PHPUnit\Framework\MockObject\ClassAlreadyExistsException;
use PHPUnit\Framework\MockObject\ClassIsFinalException;
use PHPUnit\Framework\MockObject\DuplicateMethodException;
use PHPUnit\Framework\MockObject\IncompatibleReturnValueException;
use PHPUnit\Framework\MockObject\InvalidMethodNameException;
use PHPUnit\Framework\MockObject\OriginalConstructorInvocationRequiredException;
use PHPUnit\Framework\MockObject\ReflectionException;
use PHPUnit\Framework\MockObject\RuntimeException;
use PHPUnit\Framework\MockObject\UnknownTypeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserModule\Process\GoogleLoginProcess;
use UserModule\Process\LoginProcess;
use UserModule\Service\LoginService;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class LoginServiceTest
 * @package UserModuleTest\Service
 */
class LoginServiceTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var LoginProcess $loginProcessMock */
    protected $loginProcessMock;

    /** @var GoogleLoginProcess $googleLoginProcessMock */
    protected $googleLoginProcessMock;

    /** @var Container $sessionContainerMock */
    protected $sessionContainerMock;

    /** @var Config $configMock */
    protected $configMock;

    /** @var LoginService $loginService */
    protected $loginService;

    /**
     * @throws InvalidArgumentException
     * @throws CannotUseOnlyMethodsException
     * @throws ClassAlreadyExistsException
     * @throws ClassIsFinalException
     * @throws DuplicateMethodException
     * @throws InvalidMethodNameException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loginProcessMock = $this->getMockBuilder(LoginProcess::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->googleLoginProcessMock = $this->getMockBuilder(GoogleLoginProcess::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionContainerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["offsetGet", "offsetUnset"])
            ->getMock();

        $this->container->setAllowOverride(true);
        $this->container->setService(LoginProcess::class, $this->loginProcessMock);
        $this->container->setService(GoogleLoginProcess::class, $this->googleLoginProcessMock);
        $this->container->setService(Container::class, $this->sessionContainerMock);
        $this->container->setAllowOverride(false);

        $this->loginService = $this->container->get("ServiceManager")
            ->get(LoginService::class);
        $this->configMock = new Config($this->container->get("config"));
        $this->loginService->setLoginProcess($this->loginProcessMock);
        $this->loginService->setGoogleLoginProcess($this->googleLoginProcessMock);
        $this->loginService->setSessionContainer($this->sessionContainerMock);
        $this->loginService->setConfig($this->configMock);
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Exception
     */
    public function testLogout()
    {
        $this->sessionContainerMock->method("offsetGet")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->sessionContainerMock->method("offsetUnset")
            ->willReturnSelf();

        $this->assertInstanceOf(LoginService::class, $this->loginService->logout());
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettersAndGetters()
    {
        $this->loginService->setLoginProcess($this->loginProcessMock);
        $this->assertSame($this->loginProcessMock, $this->loginService->getLoginProcess());

        $this->loginService->setGoogleLoginProcess($this->googleLoginProcessMock);
        $this->assertSame($this->googleLoginProcessMock, $this->loginService->getGoogleLoginProcess());

        $this->loginService->setSessionContainer($this->sessionContainerMock);
        $this->assertSame($this->sessionContainerMock, $this->loginService->getSessionContainer());

        $this->loginService->setConfig($this->configMock);
        $this->assertSame($this->configMock, $this->loginService->getConfig());
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws IncompatibleReturnValueException
     */
    public function testFilterAccessGrantsAccess()
    {
        $this->sessionContainerMock->method("offsetGet")
            ->willReturn(self::DEFAULT_TESTING_VALUE);

        $value = $this->loginService->filterAccess(
            "Application",
            "IndexController",
            "index",
            "view"
        );
        $this->assertIsInt($value);
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws IncompatibleReturnValueException
     */
    public function testFilterAccessDeniesAccess()
    {
        $this->sessionContainerMock->method("offsetGet")
            ->willReturn(self::DEFAULT_TESTING_VALUE);

        $value = $this->loginService->filterAccess(
            self::DEFAULT_TESTING_VALUE,
            self::DEFAULT_TESTING_VALUE,
            self::DEFAULT_TESTING_VALUE,
            self::DEFAULT_TESTING_VALUE
        );
        $this->assertIsInt($value);
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws IncompatibleReturnValueException
     */
    public function testFilterAccessRequiresAuthentication()
    {
        $this->sessionContainerMock->method("offsetGet")
            ->willReturn(null);

        $value = $this->loginService->filterAccess(
            self::DEFAULT_TESTING_VALUE,
            self::DEFAULT_TESTING_VALUE,
            self::DEFAULT_TESTING_VALUE,
            self::DEFAULT_TESTING_VALUE
        );
        $this->assertIsInt($value);
    }
}

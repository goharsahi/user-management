<?php

namespace UserModuleTest\Process;

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
use UserModule\Form\LoginForm;
use UserModule\Process\LoginProcess;
use UserModule\Request\LoginRequest;
use UserModule\Request\RequestAbstract;
use UserModule\Response\LoginResponse;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class LoginProcessTest
 * @package UserModuleTest\Process
 */
class LoginProcessTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var RequestAbstract $requestAbstractMock */
    protected $requestAbstractMock;

    /** @var LoginForm $loginFormMock */
    protected $loginFormMock;

    /** @var LoginRequest $loginRequestMock */
    protected $loginRequestMock;

    /** @var LoginResponse $loginResponseMock */
    protected $loginResponseMock;

    /** @var LoginProcess $loginProcess */
    protected $loginProcess;

    /**
     * @throws InvalidArgumentException
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
     * @throws CannotUseOnlyMethodsException
     * @throws IncompatibleReturnValueException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->requestAbstractMock = $this->getMockBuilder(RequestAbstract::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->loginFormMock = $this->getMockBuilder(LoginForm::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->loginRequestMock = $this->getMockBuilder(LoginRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["send"])
            ->getMock();
        $this->loginResponseMock = $this->getMockBuilder(LoginResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->setAllowOverride(true);
        $this->container->setService(RequestAbstract::class, $this->requestAbstractMock);
        $this->container->setService(LoginForm::class, $this->loginFormMock);
        $this->container->setService(LoginRequest::class, $this->loginRequestMock);
        $this->container->setService(LoginResponse::class, $this->loginResponseMock);
        $this->container->setAllowOverride(false);

        $this->loginRequestMock->method("send")
            ->willReturn($this->loginResponseMock);

        $this->loginProcess = $this->container->get("ServiceManager")
            ->get(LoginProcess::class);
        $this->loginProcess->setLoginRequest($this->loginRequestMock);
        $this->loginProcess->setLoginForm($this->loginFormMock);
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettersAndGetters()
    {
        $this->loginProcess->setRequest($this->requestAbstractMock);
        $this->assertSame($this->requestAbstractMock, $this->loginProcess->getRequest());

        $this->loginProcess->setLoginRequest($this->loginRequestMock);
        $this->assertSame($this->loginRequestMock, $this->loginProcess->getLoginRequest());

        $this->loginProcess->setLoginForm($this->loginFormMock);
        $this->assertSame($this->loginFormMock, $this->loginProcess->getLoginForm());
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testExecuteReturnsLoginResponse()
    {
        $this->assertInstanceOf(LoginResponse::class, $this->loginProcess->execute());
    }
}

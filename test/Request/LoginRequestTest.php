<?php

namespace UserModuleTest\Request;

use Laminas\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Laminas\Authentication\Result;
use Laminas\Db\Adapter\Adapter;
use Laminas\Session\Container;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\CannotUseAddMethodsException;
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
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;
use UserModule\Form\LoginForm;
use UserModule\Request\LoginRequest;
use UserModule\Response\LoginResponse;
use UserModule\Service\OauthDbService;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class LoginRequestTest
 * @package UserModuleTest\Request
 */
class LoginRequestTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var LoginForm $loginFormMock */
    protected $loginFormMock;

    /** @var LoginResponse $loginResponseMock */
    protected $loginResponseMock;

    /** @var Adapter $dbAdapterMock */
    protected $dbAdapterMock;

    /** @var Container $sessionContainerMock */
    protected $sessionContainerMock;

    /** @var OauthDbService $oauthDbServiceMock */
    protected $oauthDbServiceMock;

    /** @var CallbackCheckAdapter $callbackCheckAdapterMock */
    protected $callbackCheckAdapterMock;

    /** @var Result $authenticationResultMock */
    protected $authenticationResultMock;

    /** @var stdClass $stdClassMock */
    protected $stdClassMock;

    /** @var LoginRequest $loginRequest */
    protected $loginRequest;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws ClassAlreadyExistsException
     * @throws ClassIsFinalException
     * @throws DuplicateMethodException
     * @throws InvalidMethodNameException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     * @throws IncompatibleReturnValueException
     * @throws CannotUseOnlyMethodsException
     * @throws CannotUseAddMethodsException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loginFormMock = $this->getMockBuilder(LoginForm::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->loginResponseMock = $this->getMockBuilder(LoginResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dbAdapterMock = $this->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionContainerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->oauthDbServiceMock = $this->getMockBuilder(OauthDbService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->callbackCheckAdapterMock = $this->getMockBuilder(CallbackCheckAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->authenticationResultMock = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["isValid"])
            ->getMock();
        $this->stdClassMock = $this->getMockBuilder(stdClass::class)
            ->disableOriginalConstructor()
            ->addMethods(["getPassword"])
            ->getMock();

        $this->container->setAllowOverride(true);
        $this->container->setService(LoginForm::class, $this->loginFormMock);
        $this->container->setService(LoginResponse::class, $this->loginResponseMock);
        $this->container->setService(Adapter::class, $this->dbAdapterMock);
        $this->container->setService(Container::class, $this->sessionContainerMock);
        $this->container->setService(OauthDbService::class, $this->oauthDbServiceMock);
        $this->container->setService(CallbackCheckAdapter::class, $this->callbackCheckAdapterMock);
        $this->container->setService(Result::class, $this->authenticationResultMock);
        $this->container->setService(stdClass::class, $this->stdClassMock);
        $this->container->setAllowOverride(false);

        $this->loginFormMock->method("getData")
            ->willReturn(["email" => self::DEFAULT_TESTING_VALUE, "password" => self::DEFAULT_TESTING_VALUE]);
        $this->callbackCheckAdapterMock->method("setTableName")
            ->willReturnSelf();
        $this->callbackCheckAdapterMock->method("setIdentityColumn")
            ->willReturnSelf();
        $this->callbackCheckAdapterMock->method("setCredentialColumn")
            ->willReturnSelf();
        $this->callbackCheckAdapterMock->method("setCredentialValidationCallback")
            ->willReturnSelf();
        $this->callbackCheckAdapterMock->method("setIdentity")
            ->willReturnSelf();
        $this->callbackCheckAdapterMock->method("setCredential")
            ->willReturnSelf();
        $this->callbackCheckAdapterMock->method("authenticate")
            ->willReturn($this->authenticationResultMock);
        $this->authenticationResultMock->method("isValid")
            ->willReturn(true);
        $this->oauthDbServiceMock->method("getOauthUsersByUserId")
            ->willReturn($this->stdClassMock);
        $this->stdClassMock->method("getPassword")
            ->willReturn(self::DEFAULT_TESTING_VALUE);

        $this->loginRequest = $this->container->get("ServiceManager")
            ->get(LoginRequest::class);
        $this->loginRequest->setLoginResponse($this->loginResponseMock);
        $this->loginRequest->setOauthDbService($this->oauthDbServiceMock);
        $this->loginRequest->setSessionContainer($this->sessionContainerMock);
        $this->loginRequest->setDbAdapter($this->dbAdapterMock);
        $this->loginRequest->setLoginForm($this->loginFormMock);
        $this->loginRequest->setAuthAdapter($this->callbackCheckAdapterMock);
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testSettersAndGetters()
    {
        $this->loginRequest->setLoginResponse($this->loginResponseMock);
        $this->assertSame($this->loginResponseMock, $this->loginRequest->getLoginResponse());

        $this->loginRequest->setOauthDbService($this->oauthDbServiceMock);
        $this->assertSame($this->oauthDbServiceMock, $this->loginRequest->getOauthDbService());

        $this->loginRequest->setSessionContainer($this->sessionContainerMock);
        $this->assertSame($this->sessionContainerMock, $this->loginRequest->getSessionContainer());

        $this->loginRequest->setDbAdapter($this->dbAdapterMock);
        $this->assertSame($this->dbAdapterMock, $this->loginRequest->getDbAdapter());

        $this->loginRequest->setLoginForm($this->loginFormMock);
        $this->assertSame($this->loginFormMock, $this->loginRequest->getLoginForm());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \Laminas\Authentication\Adapter\DbTable\Exception\RuntimeException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws Exception
     */
    public function testSendReturnsLoginResponse()
    {
        $this->assertInstanceOf(LoginResponse::class, $this->loginRequest->send());
    }
}

<?php

namespace UserModuleTest\Service;

use Google_Client;
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
use UserModule\Service\GoogleClientService;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class OauthDbServiceTest
 * @package UserModuleTest\Service
 */
class GoogleClientServiceTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var Google_Client $googleClientMock */
    protected $googleClientMock;

    /** @var Container $sessionContainerMock */
    protected $sessionContainerMock;

    /** @var GoogleClientService $googleClientService */
    protected $googleClientService;

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

        $this->googleClientMock = $this->getMockBuilder(Google_Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    "setAccessToken",
                    "isAccessTokenExpired",
                    "setTokenCallback",
                    "getRefreshToken",
                    "fetchAccessTokenWithRefreshToken",
                    "fetchAccessTokenWithAuthCode",
                    "setApplicationName",
                    "setScopes",
                    "setClientId",
                    "setClientSecret",
                    "setRedirectUri",
                    "setAccessType",
                ]
            )
            ->getMock();
        $this->sessionContainerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["offsetGet", "offsetUnset", "offsetExists", "offsetSet"])
            ->getMock();

        $this->container->setAllowOverride(true);
        $this->container->setService(Google_Client::class, $this->googleClientMock);
        $this->container->setService(Container::class, $this->sessionContainerMock);
        $this->container->setAllowOverride(false);

        $this->googleClientMock->method("setAccessToken")
            ->willReturnSelf();
        $this->googleClientMock->method("isAccessTokenExpired")
            ->willReturn(true);
        $this->googleClientMock->method("setTokenCallback")
            ->willReturn(true);
        $this->googleClientMock->method("fetchAccessTokenWithRefreshToken")
            ->willReturn([self::DEFAULT_TESTING_VALUE => self::DEFAULT_TESTING_VALUE]);
        $this->googleClientMock->method("setApplicationName")
            ->willReturnSelf();
        $this->googleClientMock->method("setScopes")
            ->willReturnSelf();
        $this->googleClientMock->method("setClientId")
            ->willReturnSelf();
        $this->googleClientMock->method("setClientSecret")
            ->willReturnSelf();
        $this->googleClientMock->method("setRedirectUri")
            ->willReturnSelf();
        $this->googleClientMock->method("setAccessType")
            ->willReturnSelf();
        $this->sessionContainerMock->method("offsetUnset")
            ->willReturnSelf();
        $this->sessionContainerMock->method("offsetExists")
            ->willReturn(true);
        $this->sessionContainerMock->method("offsetSet")
            ->willReturnSelf();

        $this->googleClientService = $this->container->get("ServiceManager")
            ->get(GoogleClientService::class);
        $this->googleClientService->setGoogleClient($this->googleClientMock);
        $this->googleClientService->setSessionContainer($this->sessionContainerMock);
        $this->googleClientService->setRedirectUri(self::DEFAULT_TESTING_VALUE);
        $this->googleClientService->setClientId(self::DEFAULT_TESTING_VALUE);
        $this->googleClientService->setClientSecret(self::DEFAULT_TESTING_VALUE);
        $this->googleClientService->setScope(self::DEFAULT_TESTING_VALUE);
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettersAndGetters()
    {
        $this->googleClientService->setGoogleClient($this->googleClientMock);
        $this->assertSame($this->googleClientMock, $this->googleClientService->getGoogleClient());

        $this->googleClientService->setSessionContainer($this->sessionContainerMock);
        $this->assertSame($this->sessionContainerMock, $this->googleClientService->getSessionContainer());

        $this->googleClientService->setAuthCode(self::DEFAULT_TESTING_VALUE);
        $this->assertSame(self::DEFAULT_TESTING_VALUE, $this->googleClientService->getAuthCode());

        $this->googleClientService->setRedirectUri(self::DEFAULT_TESTING_VALUE);
        $this->assertSame(self::DEFAULT_TESTING_VALUE, $this->googleClientService->getRedirectUri());

        $this->googleClientService->setClientId(self::DEFAULT_TESTING_VALUE);
        $this->assertSame(self::DEFAULT_TESTING_VALUE, $this->googleClientService->getClientId());

        $this->googleClientService->setClientSecret(self::DEFAULT_TESTING_VALUE);
        $this->assertSame(self::DEFAULT_TESTING_VALUE, $this->googleClientService->getClientSecret());

        $this->googleClientService->setScope(self::DEFAULT_TESTING_VALUE);
        $this->assertSame(self::DEFAULT_TESTING_VALUE, $this->googleClientService->getScope());
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetupClientReturnsGoogleClientService()
    {
        $this->assertInstanceOf(GoogleClientService::class, $this->googleClientService->setupClient());
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testAuthenticateClientWithRefreshTokenReturnsGoogleClient()
    {
        $this->sessionContainerMock->method("offsetGet")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->googleClientMock->method("getRefreshToken")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->googleClientService->setAuthCode(self::DEFAULT_TESTING_VALUE);
        $this->assertInstanceOf(Google_Client::class, $this->googleClientService->authenticateClient());
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testAuthenticateClientWithoutRefreshTokenWithNoAuthCodeReturnsNull()
    {
        $this->sessionContainerMock->method("offsetGet")
            ->willReturn(null);
        $this->googleClientMock->method("getRefreshToken")
            ->willReturn(null);
        $this->googleClientService->setAuthCode();
        $this->assertNull($this->googleClientService->authenticateClient());
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testAuthenticateClientWithoutRefreshTokenWithAuthCodeReturnsGoogleClient()
    {
        $this->sessionContainerMock->method("offsetGet")
            ->willReturn(null);
        $this->googleClientMock->method("getRefreshToken")
            ->willReturn(null);
        $this->googleClientService->setAuthCode(self::DEFAULT_TESTING_VALUE);
        $this->assertInstanceOf(Google_Client::class, $this->googleClientService->authenticateClient());
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testAuthenticateClientWithoutRefreshTokenWithAuthCodeAndErrorUnsetsSessionAndReturnsGoogleClient()
    {
        $this->sessionContainerMock->method("offsetGet")
            ->willReturn(null);
        $this->googleClientMock->method("getRefreshToken")
            ->willReturn(null);
        $this->googleClientService->setAuthCode(self::DEFAULT_TESTING_VALUE);
        $this->googleClientMock->method("fetchAccessTokenWithAuthCode")
            ->willReturn([self::DEFAULT_TESTING_VALUE => self::DEFAULT_TESTING_VALUE]);
        $this->assertInstanceOf(Google_Client::class, $this->googleClientService->authenticateClient());
    }
}

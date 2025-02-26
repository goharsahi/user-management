<?php

namespace UserModuleTest\Test;

use Google\Auth\OAuth2;
use Google_Client;
use Laminas\Session\Container;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\InvalidArgumentException;
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
use stdClass;
use UserModule\Request\GoogleLoginRequest;
use UserModule\Response\GoogleLoginResponse;
use UserModule\Service\GoogleClientService;
use UserModule\Service\OauthDbService;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class GoogleLoginRequestTest
 * @package UserModuleTest\Test
 */
class GoogleLoginRequestTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    const DEFAULT_TESTING_VALUE = "Test";

    /** @var string DEFAULT_TESTING_VALUE_INT */
    const DEFAULT_TESTING_VALUE_INT = 1;

    /** @var GoogleLoginResponse $googleLoginResponseMock */
    protected $googleLoginResponseMock;

    /** @var GoogleClientService $googleClientServiceMock */
    protected $googleClientServiceMock;

    /** @var OauthDbService $oauthDbServiceMock */
    protected $oauthDbServiceMock;

    /** @var Container $sessionContainerMock */
    protected $sessionContainerMock;

    /** @var Google_Client $googleClientMock */
    protected $googleClientMock;

    /** @var OAuth2 $oauth2Mock */
    protected $oauth2Mock;

    /** @var StdClass $stdClassMock */
    protected $stdClassMock;

    /** @var GoogleLoginRequest $googleLoginRequest */
    protected $googleLoginRequest;

    /**
     * @throws ClassAlreadyExistsException
     * @throws ClassIsFinalException
     * @throws DuplicateMethodException
     * @throws InvalidArgumentException
     * @throws InvalidMethodNameException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     * @throws CannotUseOnlyMethodsException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws IncompatibleReturnValueException
     * @throws CannotUseAddMethodsException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->googleLoginResponseMock = $this->getMockBuilder(GoogleLoginResponse::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["setResponse"])
            ->getMock();
        $this->googleClientServiceMock = $this->getMockBuilder(GoogleClientService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    "setAuthCode",
                    "setScope",
                    "setClientId",
                    "setClientSecret",
                    "setRedirectUri",
                    "setupClient",
                    "authenticateClient",
                    "getGoogleClient",
                ]
            )
            ->getMock();
        $this->oauthDbServiceMock = $this->getMockBuilder(OauthDbService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    "getOauthClientByClientId",
                    "upsertOauthAuthorizationCodes",
                    "upsertOauthAccessTokens",
                    "upsertOauthRefreshTokens",
                ]
            )
            ->getMock();
        $this->sessionContainerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->googleClientMock = $this->getMockBuilder(Google_Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    "setAccessToken",
                    "setScopes",
                    "createAuthUrl",
                    "getAccessToken",
                    "verifyIdToken",
                    "getOAuth2Service",
                    "getClientId",
                    "getRedirectUri",
                ]
            )
            ->getMock();
        $this->oauth2Mock = $this->getMockBuilder(OAuth2::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["getIdToken", "getCode", "getExpiry"])
            ->getMock();
        $this->stdClassMock = $this->getMockBuilder(StdClass::class)
            ->disableOriginalConstructor()
            ->addMethods(["getClientId", "getClientSecret", "getRedirectUri", "getScope"])
            ->getMock();

        $this->container->setAllowOverride(true);
        $this->container->setService(GoogleLoginResponse::class, $this->googleLoginResponseMock);
        $this->container->setService(GoogleClientService::class, $this->googleClientServiceMock);
        $this->container->setService(OauthDbService::class, $this->oauthDbServiceMock);
        $this->container->setService(Container::class, $this->sessionContainerMock);
        $this->container->setService(Google_Client::class, $this->googleClientMock);
        $this->container->setService(OAuth2::class, $this->oauth2Mock);
        $this->container->setService(StdClass::class, $this->stdClassMock);
        $this->container->setAllowOverride(false);

        $this->oauthDbServiceMock->method("getOauthClientByClientId")
            ->willReturn($this->stdClassMock);
        $this->oauthDbServiceMock->method("upsertOauthAuthorizationCodes")
            ->willReturn(true);
        $this->oauthDbServiceMock->method("upsertOauthAccessTokens")
            ->willReturn(true);
        $this->oauthDbServiceMock->method("upsertOauthRefreshTokens")
            ->willReturn(true);

        $this->googleClientMock->method("setAccessToken")
            ->willReturnSelf();
        $this->googleClientMock->method("setScopes")
            ->willReturnSelf();
        $this->googleClientMock->method("getClientId")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->googleClientMock->method("getRedirectUri")
            ->willReturn(self::DEFAULT_TESTING_VALUE);

        $this->googleClientServiceMock->method("getGoogleClient")
            ->willReturn($this->googleClientMock);
        $this->googleClientServiceMock->method("setClientId")
            ->willReturnSelf();
        $this->googleClientServiceMock->method("setClientSecret")
            ->willReturnSelf();
        $this->googleClientServiceMock->method("setRedirectUri")
            ->willReturnSelf();

        $this->googleClientServiceMock->method("setupClient")
            ->willReturnSelf();

        $this->sessionContainerMock->method("offsetSet")
            ->willReturnSelf();

        $this->googleClientMock->method("createAuthUrl")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->googleClientMock->method("verifyIdToken")
            ->willReturn(["id_token" => self::DEFAULT_TESTING_VALUE, "email" => self::DEFAULT_TESTING_VALUE]);
        $this->googleClientMock->method("getOAuth2Service")
            ->willReturn($this->oauth2Mock);

        $this->oauth2Mock->method("getIdToken")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->oauth2Mock->method("getCode")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->oauth2Mock->method("getExpiry")
            ->willReturn(self::DEFAULT_TESTING_VALUE_INT);

        $this->stdClassMock->method("getClientId")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->stdClassMock->method("getClientSecret")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->stdClassMock->method("getRedirectUri")
            ->willReturn(self::DEFAULT_TESTING_VALUE);
        $this->stdClassMock->method("getScope")
            ->willReturn(self::DEFAULT_TESTING_VALUE);

        $this->googleLoginRequest = $this->container->get("ServiceManager")
            ->get(GoogleLoginRequest::class);
        $this->googleLoginRequest->setGoogleLoginResponse($this->googleLoginResponseMock);
        $this->googleLoginRequest->setGoogleClientService($this->googleClientServiceMock);
        $this->googleLoginRequest->setOauthDbService($this->oauthDbServiceMock);
        $this->googleLoginRequest->setSessionContainer($this->sessionContainerMock);
        $this->googleLoginRequest->setAuthCode(self::DEFAULT_TESTING_VALUE);
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    function testGettersAndSetters()
    {
        $this->googleLoginRequest->setGoogleLoginResponse($this->googleLoginResponseMock);
        $this->assertSame($this->googleLoginResponseMock, $this->googleLoginRequest->getGoogleLoginResponse());

        $this->googleLoginRequest->setGoogleClientService($this->googleClientServiceMock);
        $this->assertSame($this->googleClientServiceMock, $this->googleLoginRequest->getGoogleClientService());

        $this->googleLoginRequest->setOauthDbService($this->oauthDbServiceMock);
        $this->assertSame($this->oauthDbServiceMock, $this->googleLoginRequest->getOauthDbService());

        $this->googleLoginRequest->setSessionContainer($this->sessionContainerMock);
        $this->assertSame($this->sessionContainerMock, $this->googleLoginRequest->getSessionContainer());

        $this->googleLoginRequest->setAuthCode(self::DEFAULT_TESTING_VALUE);
        $this->assertSame(self::DEFAULT_TESTING_VALUE, $this->googleLoginRequest->getAuthCode());
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    function testSendReturnsGoogleLoginResponseOnSuccess()
    {
        $this->googleClientServiceMock->method("authenticateClient")
            ->willReturn($this->googleClientMock);
        $this->googleClientMock->method("getAccessToken")
            ->willReturn(
                [
                    "access_token" => self::DEFAULT_TESTING_VALUE,
                    "refresh_token" => self::DEFAULT_TESTING_VALUE,
                    "expires_in" => self::DEFAULT_TESTING_VALUE_INT,
                    "created" => self::DEFAULT_TESTING_VALUE_INT,
                    "scope" => self::DEFAULT_TESTING_VALUE,
                    "id_token" => self::DEFAULT_TESTING_VALUE,
                ]
            );
        $this->assertInstanceOf(GoogleLoginResponse::class, $this->googleLoginRequest->send());
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws IncompatibleReturnValueException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    function testSendReturnsGoogleLoginResponseOnFailureToSetupGoogleClient()
    {
        $this->googleClientServiceMock->method("authenticateClient")
            ->willReturn(null);
        $this->googleClientMock->method("getAccessToken")
            ->willReturn(null);
        $this->assertInstanceOf(GoogleLoginResponse::class, $this->googleLoginRequest->send());
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws IncompatibleReturnValueException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    function testSendReturnsGoogleLoginResponseOnFailureToGetAccessToken()
    {
        $this->googleClientServiceMock->method("authenticateClient")
            ->willReturn($this->googleClientMock);
        $this->googleClientMock->method("getAccessToken")
            ->willReturn(null);
        $this->assertInstanceOf(GoogleLoginResponse::class, $this->googleLoginRequest->send());
    }
}

<?php

namespace UserModuleTest\Service;

use Laminas\Config\Config;
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
use UserModule\Repository\OauthAccessTokensRepository;
use UserModule\Repository\OauthAuthorizationCodesRepository;
use UserModule\Repository\OauthClientsRepository;
use UserModule\Repository\OauthRefreshTokensRepository;
use UserModule\Repository\OauthUsersRepository;
use UserModule\Service\OauthDbService;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class OauthDbServiceTest
 * @package UserModuleTest\Service
 */
class OauthDbServiceTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var OauthClientsRepository $oauthClientsRepositoryMock */
    protected $oauthClientsRepositoryMock;

    /** @var OauthAuthorizationCodesRepository $oauthAuthorizationCodesRepositoryMock */
    protected $oauthAuthorizationCodesRepositoryMock;

    /** @var OauthAccessTokensRepository $oauthAccessTokensRepositoryMock */
    protected $oauthAccessTokensRepositoryMock;

    /** @var OauthRefreshTokensRepository $oauthRefreshTokensRepositoryMock */
    protected $oauthRefreshTokensRepositoryMock;

    /** @var OauthUsersRepository $oauthUsersRepositoryMock */
    protected $oauthUsersRepositoryMock;

    /** @var Config $configMock */
    protected $configMock;

    /** @var OauthDbService $oauthDbService */
    protected $oauthDbService;

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

        $this->oauthClientsRepositoryMock = $this->getMockBuilder(OauthClientsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["findOauthClientsByClientId"])
            ->getMock();
        $this->oauthAuthorizationCodesRepositoryMock = $this->getMockBuilder(OauthAuthorizationCodesRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["findOauthAuthorizationCodesByUserIdAndClientId", "upsert"])
            ->getMock();
        $this->oauthAccessTokensRepositoryMock = $this->getMockBuilder(OauthAccessTokensRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["upsert"])
            ->getMock();
        $this->oauthRefreshTokensRepositoryMock = $this->getMockBuilder(OauthRefreshTokensRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["upsert"])
            ->getMock();
        $this->oauthUsersRepositoryMock = $this->getMockBuilder(OauthUsersRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["findOauthUsersByUserId"])
            ->getMock();

        $this->container->setAllowOverride(true);
        $this->container->setService(OauthClientsRepository::class, $this->oauthClientsRepositoryMock);
        $this->container->setService(
            OauthAuthorizationCodesRepository::class,
            $this->oauthAuthorizationCodesRepositoryMock
        );
        $this->container->setService(OauthAccessTokensRepository::class, $this->oauthAccessTokensRepositoryMock);
        $this->container->setService(OauthRefreshTokensRepository::class, $this->oauthRefreshTokensRepositoryMock);
        $this->container->setService(OauthUsersRepository::class, $this->oauthUsersRepositoryMock);
        $this->container->setAllowOverride(false);

        $this->oauthClientsRepositoryMock->method("findOauthClientsByClientId")
            ->willReturn((object)self::DEFAULT_TESTING_VALUE);
        $this->oauthAuthorizationCodesRepositoryMock->method("findOauthAuthorizationCodesByUserIdAndClientId")
            ->willReturn((object)self::DEFAULT_TESTING_VALUE);
        $this->oauthUsersRepositoryMock->method("findOauthUsersByUserId")
            ->willReturn((object)self::DEFAULT_TESTING_VALUE);
        $this->oauthAuthorizationCodesRepositoryMock->method("upsert")
            ->willReturn(true);
        $this->oauthAccessTokensRepositoryMock->method("upsert")
            ->willReturn(true);
        $this->oauthRefreshTokensRepositoryMock->method("upsert")
            ->willReturn(true);

        $this->oauthDbService = $this->container->get("ServiceManager")
            ->get(OauthDbService::class);
        $this->configMock = new Config($this->container->get("config"));
        $this->oauthDbService->setOauthClientsRepository($this->oauthClientsRepositoryMock);
        $this->oauthDbService->setOauthAuthorizationCodesRepository($this->oauthAuthorizationCodesRepositoryMock);
        $this->oauthDbService->setOauthAccessTokensRepository($this->oauthAccessTokensRepositoryMock);
        $this->oauthDbService->setOauthRefreshTokensRepository($this->oauthRefreshTokensRepositoryMock);
        $this->oauthDbService->setOauthUsersRepository($this->oauthUsersRepositoryMock);
        $this->oauthDbService->setConfig($this->configMock);
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettersAndGetters()
    {
        $this->oauthDbService->setOauthClientsRepository($this->oauthClientsRepositoryMock);
        $this->assertSame($this->oauthClientsRepositoryMock, $this->oauthDbService->getOauthClientsRepository());

        $this->oauthDbService->setOauthAuthorizationCodesRepository($this->oauthAuthorizationCodesRepositoryMock);
        $this->assertSame(
            $this->oauthAuthorizationCodesRepositoryMock,
            $this->oauthDbService->getOauthAuthorizationCodesRepository()
        );

        $this->oauthDbService->setOauthAccessTokensRepository($this->oauthAccessTokensRepositoryMock);
        $this->assertSame(
            $this->oauthAccessTokensRepositoryMock,
            $this->oauthDbService->getOauthAccessTokensRepository()
        );

        $this->oauthDbService->setOauthRefreshTokensRepository($this->oauthRefreshTokensRepositoryMock);
        $this->assertSame(
            $this->oauthRefreshTokensRepositoryMock,
            $this->oauthDbService->getOauthRefreshTokensRepository()
        );

        $this->oauthDbService->setOauthUsersRepository($this->oauthUsersRepositoryMock);
        $this->assertSame($this->oauthUsersRepositoryMock, $this->oauthDbService->getOauthUsersRepository());

        $this->oauthDbService->setConfig($this->configMock);
        $this->assertSame($this->configMock, $this->oauthDbService->getConfig());
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetOauthClientsByClientIdReturnsObject()
    {
        $this->assertIsObject($this->oauthDbService->getOauthClientByClientId());
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetOauthAuthorizationCodesByUserIdAndClientIdReturnsObject()
    {
        $this->assertIsObject(
            $this->oauthDbService->getOauthAuthorizationCodesByUserIdAndClientId(self::DEFAULT_TESTING_VALUE)
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testUpsertOauthAuthorizationCodesReturnsBoolean()
    {
        $this->assertIsBool(
            $this->oauthDbService->upsertOauthAuthorizationCodes(
                [self::DEFAULT_TESTING_VALUE => self::DEFAULT_TESTING_VALUE]
            )
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testUpsertOauthAccessTokensReturnsBoolean()
    {
        $this->assertIsBool(
            $this->oauthDbService->upsertOauthAccessTokens(
                [self::DEFAULT_TESTING_VALUE => self::DEFAULT_TESTING_VALUE]
            )
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testUpsertOauthRefreshTokensReturnsBoolean()
    {
        $this->assertIsBool(
            $this->oauthDbService->upsertOauthRefreshTokens(
                [self::DEFAULT_TESTING_VALUE => self::DEFAULT_TESTING_VALUE]
            )
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetOauthUsersByUserIdReturnsObject()
    {
        $this->assertIsObject(
            $this->oauthDbService->getOauthUsersByUserId(self::DEFAULT_TESTING_VALUE)
        );
    }
}

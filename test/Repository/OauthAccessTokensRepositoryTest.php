<?php

namespace UserModuleTest\Repository;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\ResultSet\AbstractResultSet;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Hydrator\HydrationInterface;
use Laminas\Hydrator\ReflectionHydrator;
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
use UserModule\Model\OauthAccessTokensModel;
use UserModule\Repository\OauthAccessTokensRepository;
use UserModuleTest\AbstractApplicationTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class OauthAccessTokensRepositoryTest
 * @package UserModuleTest\Repository
 */
class OauthAccessTokensRepositoryTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var OauthAccessTokensModel $oauthAccessTokensModelMock */
    protected $oauthAccessTokensModelMock;

    /** @var Adapter $adapterMock */
    protected $adapterMock;

    /** @var Sql $sqlMock */
    protected $sqlMock;

    /** @var Select $selectMock */
    protected $selectMock;

    /** @var Insert $insertMock */
    protected $insertMock;

    /** @var StatementInterface $statementInterfaceMock */
    protected $statementInterfaceMock;

    /** @var ResultInterface $resultInterfaceMock */
    protected $resultInterfaceMock;

    /** @var AbstractResultSet $abstractResultSetMock */
    protected $abstractResultSetMock;

    /** @var HydratingResultSet $hydratingResultSetMock */
    protected $hydratingResultSetMock;

    /** @var HydrationInterface $hydrationInterfaceMock */
    protected $hydrationInterfaceMock;

    /** @var ReflectionHydrator $reflectionHydratorMock */
    protected $reflectionHydratorMock;

    /** @var OauthAccessTokensRepository $oauthAccessTokensRepository */
    protected $oauthAccessTokensRepository;

    /**
     * @throws CannotUseOnlyMethodsException
     * @throws ClassAlreadyExistsException
     * @throws ClassIsFinalException
     * @throws ContainerExceptionInterface
     * @throws DuplicateMethodException
     * @throws InvalidArgumentException
     * @throws InvalidMethodNameException
     * @throws NotFoundExceptionInterface
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     * @throws IncompatibleReturnValueException
     * @throws CannotUseAddMethodsException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->adapterMock = $this->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->oauthAccessTokensModelMock = $this->getMockBuilder(OauthAccessTokensModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sqlMock = $this->getMockBuilder(Sql::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["select", "prepareStatementForSqlObject", "insert"])
            ->getMock();
        $this->insertMock = $this->getMockBuilder(Insert::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["columns", "values"])
            ->getMock();
        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["where"])
            ->addMethods(["values"])
            ->getMock();
        $this->statementInterfaceMock = $this->getMockForAbstractClass(StatementInterface::class);
        $this->resultInterfaceMock = $this->getMockForAbstractClass(ResultInterface::class);
        $this->hydratingResultSetMock = $this->getMockBuilder(HydratingResultSet::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->hydrationInterfaceMock = $this->getMockBuilder(HydrationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->reflectionHydratorMock = $this->getMockBuilder(ReflectionHydrator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->abstractResultSetMock = $this->getMockForAbstractClass(AbstractResultSet::class);

        $this->container->setAllowOverride(true);
        $this->container->setService(Adapter::class, $this->adapterMock);
        $this->container->setService(OauthAccessTokensModel::class, $this->oauthAccessTokensModelMock);
        $this->container->setService(Sql::class, $this->sqlMock);
        $this->container->setService(Select::class, $this->selectMock);
        $this->container->setService(Insert::class, $this->insertMock);
        $this->container->setService(StatementInterface::class, $this->statementInterfaceMock);
        $this->container->setService(ResultInterface::class, $this->resultInterfaceMock);
        $this->container->setService(HydratingResultSet::class, $this->hydratingResultSetMock);
        $this->container->setService(HydrationInterface::class, $this->hydrationInterfaceMock);
        $this->container->setService(ReflectionHydrator::class, $this->reflectionHydratorMock);
        $this->container->setService(AbstractResultSet::class, $this->abstractResultSetMock);
        $this->container->setAllowOverride(false);

        $this->sqlMock->method("select")
            ->willReturn($this->selectMock);
        $this->sqlMock->method("prepareStatementForSqlObject")
            ->willReturn($this->statementInterfaceMock);
        $this->sqlMock->method("insert")
            ->willReturn($this->selectMock);

        $this->insertMock->method("columns")
            ->willReturn($this->insertMock);
        $this->selectMock->method("values")
            ->willReturn($this->insertMock);
        $this->selectMock->method("where")
            ->willReturnSelf();

        $this->statementInterfaceMock->method("execute")
            ->willReturn($this->resultInterfaceMock);

        $this->resultInterfaceMock->method("isQueryResult")
            ->willReturn(true);

        $this->oauthAccessTokensRepository = $this->container->get("ServiceManager")
            ->get(OauthAccessTokensRepository::class);
        $this->oauthAccessTokensRepository->setDbAdapter($this->adapterMock);
        $this->oauthAccessTokensRepository->setSql($this->sqlMock);
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testFindOauthAccessTokensByClientIdReturnsNull()
    {
        $this->oauthAccessTokensModelMock->method("exchangeArray")
            ->willReturnSelf();

        $this->assertNull(
            $this->oauthAccessTokensRepository->findOauthAccessTokensByClientId(self::DEFAULT_TESTING_VALUE)
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testFindOauthAccessTokensByUserIdReturnsNull()
    {
        $this->oauthAccessTokensModelMock->method("exchangeArray")
            ->willReturnSelf();

        $this->assertNull(
            $this->oauthAccessTokensRepository->findOauthAccessTokensByUserId(self::DEFAULT_TESTING_VALUE)
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testFindOauthAccessTokensByUserIdAndClientIdReturnsNull()
    {
        $this->oauthAccessTokensModelMock->method("exchangeArray")
            ->willReturnSelf();

        $this->assertNull(
            $this->oauthAccessTokensRepository->findOauthAccessTokensByUserIdAndClientId(
                self::DEFAULT_TESTING_VALUE,
                self::DEFAULT_TESTING_VALUE
            )
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testFindOauthAccessTokensByAccessTokenReturnsNull()
    {
        $this->oauthAccessTokensModelMock->method("exchangeArray")
            ->willReturnSelf();

        $this->assertNull(
            $this->oauthAccessTokensRepository->findOauthAccessTokensByAccessToken(self::DEFAULT_TESTING_VALUE)
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testUpsert()
    {
        $this->assertIsBool(
            $this->oauthAccessTokensRepository->upsert(
                [
                    "access_token" => self::DEFAULT_TESTING_VALUE,
                    "client_id" => self::DEFAULT_TESTING_VALUE,
                    "user_id" => self::DEFAULT_TESTING_VALUE,
                    "expires" => self::DEFAULT_TESTING_VALUE,
                    "scope" => self::DEFAULT_TESTING_VALUE,
                    "created" => self::DEFAULT_TESTING_VALUE,
                ]
            )
        );
    }
}

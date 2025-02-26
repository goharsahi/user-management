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
use UserModule\Process\GoogleLoginProcess;
use UserModule\Request\GoogleLoginRequest;
use UserModule\Response\GoogleLoginResponse;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class GoogleLoginProcessTest
 * @package UserModuleTest\Process
 */
class GoogleLoginProcessTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "Test";

    /** @var GoogleLoginRequest $googleLoginRequestMock */
    protected $googleLoginRequestMock;

    /** @var GoogleLoginResponse $googleLoginResponseMock */
    protected $googleLoginResponseMock;

    /** @var GoogleLoginProcess $googleLoginProcess */
    protected $googleLoginProcess;

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

        $this->googleLoginRequestMock = $this->getMockBuilder(GoogleLoginRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["send", "setAuthCode"])
            ->getMock();
        $this->googleLoginResponseMock = $this->getMockBuilder(GoogleLoginResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->setAllowOverride(true);
        $this->container->setService(GoogleLoginRequest::class, $this->googleLoginRequestMock);
        $this->container->setService(GoogleLoginResponse::class, $this->googleLoginResponseMock);
        $this->container->setAllowOverride(false);

        $this->googleLoginRequestMock->method("send")
            ->willReturn($this->googleLoginResponseMock);
        $this->googleLoginRequestMock->method("setAuthCode")
            ->willReturn($this->googleLoginRequestMock);

        $this->googleLoginProcess = $this->container->get("ServiceManager")
            ->get(GoogleLoginProcess::class);
        $this->googleLoginProcess->setGoogleLoginRequest($this->googleLoginRequestMock);
        $this->googleLoginProcess->setAuthCode(self::DEFAULT_TESTING_VALUE);
    }

    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettersAndGetters()
    {
        $this->googleLoginProcess->setGoogleLoginRequest($this->googleLoginRequestMock);
        $this->assertSame($this->googleLoginRequestMock, $this->googleLoginProcess->getGoogleLoginRequest());
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws \Laminas\Db\Sql\Exception\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testExecuteReturnsGoogleLoginResponse()
    {
        $this->assertInstanceOf(GoogleLoginResponse::class, $this->googleLoginProcess->execute());
    }
}

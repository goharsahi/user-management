<?php

namespace UserModuleTest\Controller;

use Laminas\Config\Config;
use Laminas\Form\Element\File;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Session\Container;
use Laminas\Stdlib\PriorityList;
use PHPUnit\Framework\ExpectationFailedException;
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
use UserModule\Controller\UserController;
use UserModule\Form\LoginForm;
use UserModule\Module;
use Exception;
use UserModule\Process\GoogleLoginProcess;
use UserModule\Process\LoginProcess;
use UserModule\Response\GoogleLoginResponse;
use UserModule\Response\LoginResponse;
use UserModule\Service\LoginService;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class UserControllerTest
 * @package UserModuleTest\Controller
 */
class UserControllerTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_VALUE = "today";

    /** @var string DEFAULT_TESTING_VALUE */
    public const DEFAULT_TESTING_ARRAY = ["login" => true];

    /** @var UserController $userController */
    protected $userController;

    /** @var LoginService $loginServiceMock */
    protected $loginServiceMock;

    /** @var LoginForm $loginFormMock */
    protected $loginFormMock;

    /** @var Config $configMock */
    protected $configMock;

    /** @var LoginProcess $loginProcessMock */
    protected $loginProcessMock;

    /** @var GoogleLoginProcess $googleLoginProcessMock */
    protected $googleLoginProcessMock;

    /** @var LoginResponse $loginResponseMock */
    protected $loginResponseMock;

    /** @var GoogleLoginResponse $googleLoginResponseMock */
    protected $googleLoginResponseMock;

    /** @var Container $sessionContainerMock */
    protected $sessionContainerMock;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws CannotUseOnlyMethodsException
     * @throws ClassAlreadyExistsException
     * @throws ClassIsFinalException
     * @throws DuplicateMethodException
     * @throws IncompatibleReturnValueException
     * @throws InvalidMethodNameException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loginServiceMock = $this->getMockBuilder(LoginService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["getSessionContainer", "getLoginProcess", "getGoogleLoginProcess"])
            ->getMock();
        $this->loginFormMock = $this->getMockBuilder(LoginForm::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["get", "setData", "getIterator", "isValid"])
            ->getMock();
        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["get"])
            ->getMock();
        $this->loginProcessMock = $this->getMockBuilder(LoginProcess::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["setLoginForm", "execute"])
            ->getMock();
        $this->googleLoginProcessMock = $this->getMockBuilder(GoogleLoginProcess::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["execute"])
            ->getMock();
        $this->loginResponseMock = $this->getMockBuilder(LoginResponse::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["getResponse"])
            ->getMock();
        $this->googleLoginResponseMock = $this->getMockBuilder(GoogleLoginResponse::class)
            ->disableOriginalConstructor()
            ->onlyMethods(["getResponse"])
            ->getMock();
        $this->sessionContainerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $priorityListMock = $this->getMockBuilder(PriorityList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->setAllowOverride(true);
        $this->container->setService(
            LoginService::class,
            $this->loginServiceMock
        );
        $this->container->setService(
            LoginForm::class,
            $this->loginFormMock
        );
        $this->container->setService(
            Config::class,
            $this->configMock
        );
        $this->container->setService(
            LoginProcess::class,
            $this->loginProcessMock
        );
        $this->container->setService(
            GoogleLoginProcess::class,
            $this->googleLoginProcessMock
        );
        $this->container->setService(
            LoginResponse::class,
            $this->loginResponseMock
        );
        $this->container->setService(
            GoogleLoginResponse::class,
            $this->googleLoginResponseMock
        );
        $this->container->setService(
            Container::class,
            $this->sessionContainerMock
        );
        $this->container->setService(
            PriorityList::class,
            $priorityListMock
        );
        $this->container->setAllowOverride(false);

        $elementMock = new File();
        $elementMock->setName("unit test");
        $elementMock->setLabel("unit test");
        $elementMock->setValue(self::DEFAULT_TESTING_VALUE);

        $this->loginServiceMock->method("getLoginProcess")
            ->willReturn($this->loginProcessMock);
        $this->loginServiceMock->method("getSessionContainer")
            ->willReturn($this->sessionContainerMock);
        $this->loginServiceMock->method("getGoogleLoginProcess")
            ->willReturn($this->googleLoginProcessMock);

        $this->loginFormMock->method("get")
            ->willReturn($elementMock);
        $this->loginFormMock->method("getIterator")
            ->willReturn($priorityListMock);
        $this->loginFormMock->method("setData")
            ->willReturnSelf();

        $this->configMock->method("get")
            ->willReturn(self::DEFAULT_TESTING_VALUE);

        $this->loginProcessMock->method("setLoginForm")
            ->willReturnSelf();
        $this->loginProcessMock->method("execute")
            ->willReturn($this->loginResponseMock);

        $this->googleLoginProcessMock->method("execute")
            ->willReturn($this->googleLoginResponseMock);

        $this->userController = $this->container->get("ControllerManager")
            ->get(
                UserController::class
            );
        $this->userController->setLoginForm($this->loginFormMock);
        $this->userController->setLoginService($this->loginServiceMock);
        $this->userController->setConfig($this->configMock);
    }

    /**
     * @param string $route
     * @param string $routeName
     * @param int $statusCode
     * @throws Exception
     * @throws ExpectationFailedException
     * @dataProvider dataProviderControllerRoutesForGet
     */
    public function testControllerDispatchForGet(string $route, string $routeName, int $statusCode)
    {
        $this->dispatch($route);

        $this->assertResponseStatusCode($statusCode);
        $this->assertModuleName(Module::MODULE_NAME);
        $this->assertControllerName(UserController::class);
        $this->assertControllerClass("UserController");
        $this->assertMatchedRouteName($routeName);
    }

    /** @return string[][] */
    public function dataProviderControllerRoutesForGet()
    {
        return [
            [Module::ROUTE_PREFIX, Module::ROUTE_NAME_MODULE, 200],
            [Module::ROUTE_PREFIX . "/login", Module::ROUTE_MODULE_CHILD_ACTIONS, 200],
            [Module::ROUTE_PREFIX . "/logout", Module::ROUTE_MODULE_CHILD_ACTIONS, 302],
            [Module::ROUTE_PREFIX . "/googleLogin", Module::ROUTE_MODULE_CHILD_ACTIONS, 302],
            [Module::ROUTE_PREFIX . "/unauthorized", Module::ROUTE_MODULE_CHILD_ACTIONS, 200],
        ];
    }

    /**
     * @param string $action
     * @param int $statusCode
     * @throws Exception
     * @throws ExpectationFailedException
     * @dataProvider actionsProvider
     */
    public function testControllerActionsCanBeAccessed(string $action, int $statusCode)
    {
        $this->dispatch(Module::ROUTE_PREFIX . "/" . $action);

        $this->assertResponseStatusCode($statusCode);
        $this->assertModuleName(Module::MODULE_NAME);
        $this->assertControllerName(UserController::class);
        $this->assertControllerClass("UserController");
        $this->assertMatchedRouteName(Module::ROUTE_MODULE_CHILD_ACTIONS);
        $this->assertActionName($action);
    }

    /**
     * @return string[][]
     */
    public function actionsProvider(): array
    {
        return [
            ["index", 200],
            ["login", 200],
            ["logout", 302],
            ["googleLogin", 302],
            ["unauthorized", 200],
        ];
    }

    /**
     * @param string $route
     * @param string $routeName
     * @param array $response
     * @param int $statusCode
     * @throws ExpectationFailedException
     * @throws IncompatibleReturnValueException
     * @throws Exception
     * @dataProvider dataProviderControllerRoutesForLogin
     */
    public function testControllerDispatchForLogin(string $route, string $routeName, array $response, int $statusCode)
    {
        $this->loginResponseMock->method("getResponse")
            ->willReturn($response);
        $this->loginFormMock->method("isValid")
            ->willReturn(true);

        $this->dispatch(
            $route,
            "POST",
            self::DEFAULT_TESTING_ARRAY
        );
        $this->assertResponseStatusCode($statusCode);
        $this->assertMatchedRouteName($routeName);
        $this->assertMatchedRouteName(Module::ROUTE_MODULE_CHILD_ACTIONS);
    }

    /** @return string[][] */
    public function dataProviderControllerRoutesForLogin()
    {
        return [
            [Module::ROUTE_PREFIX . "/login", Module::ROUTE_MODULE_CHILD_ACTIONS, ["login" => false], 200],
            [Module::ROUTE_PREFIX . "/login", Module::ROUTE_MODULE_CHILD_ACTIONS, ["login" => true], 302],
        ];
    }

    /**
     * @param string $route
     * @param string $routeName
     * @param bool $formValid
     * @param int $statusCode
     * @throws ExpectationFailedException
     * @throws IncompatibleReturnValueException
     * @throws Exception
     * @dataProvider dataProviderControllerRoutesForLoginValidity
     */
    public function testControllerDispatchForLoginValidity(
        string $route,
        string $routeName,
        bool $formValid,
        int $statusCode
    ) {
        $this->loginFormMock->method("isValid")
            ->willReturn($formValid);

        $this->dispatch(
            $route,
            "POST",
            self::DEFAULT_TESTING_ARRAY
        );
        $this->assertResponseStatusCode($statusCode);
        $this->assertMatchedRouteName($routeName);
        $this->assertMatchedRouteName(Module::ROUTE_MODULE_CHILD_ACTIONS);
    }

    /** @return string[][] */
    public function dataProviderControllerRoutesForLoginValidity()
    {
        return [
            [Module::ROUTE_PREFIX . "/login", Module::ROUTE_MODULE_CHILD_ACTIONS, false, 401],
            [Module::ROUTE_PREFIX . "/login", Module::ROUTE_MODULE_CHILD_ACTIONS, true, 200],
        ];
    }

    /**
     * @param string $route
     * @param string $routeName
     * @param array $response
     * @param int $statusCode
     * @throws ExpectationFailedException
     * @throws IncompatibleReturnValueException
     * @throws Exception
     * @dataProvider dataProviderControllerRoutesForGoogleLogin
     */
    public function testControllerDispatchForGoogleLogin(
        string $route,
        string $routeName,
        array $response,
        int $statusCode
    ) {
        $this->googleLoginResponseMock->method("getResponse")
            ->willReturn($response);

        $this->dispatch($route);

        $this->assertResponseStatusCode($statusCode);
        $this->assertModuleName(Module::MODULE_NAME);
        $this->assertControllerName(UserController::class);
        $this->assertControllerClass("UserController");
        $this->assertMatchedRouteName($routeName);
    }

    /** @return string[][] */
    public function dataProviderControllerRoutesForGoogleLogin()
    {
        return [
            [
                Module::ROUTE_PREFIX . "/googleLogin",
                Module::ROUTE_MODULE_CHILD_ACTIONS,
                ["error" => "authentication", "url" => "http://localhost"],
                302,
            ],
            [Module::ROUTE_PREFIX . "/googleLogin", Module::ROUTE_MODULE_CHILD_ACTIONS, ["login" => true], 302],
        ];
    }
}

<?php

namespace UserModule\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use UserModule\Controller\UserController;
use UserModule\Service\LoginService;

class LoginListener implements ListenerAggregateInterface
{
    /** @var array $listeners */
    protected array $listeners;

    /** @var LoginService $loginService */
    protected LoginService $loginService;

    /**
     * LoginListener constructor.
     * @param LoginService $loginService
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @inheritDoc
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, "userHasAuthentication"], -1000);
    }

    /**
     * @inheritDoc
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param MvcEvent $e
     */
    public function userHasAuthentication(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();

        if (!empty($routeMatch)) {
            $controllerName = explode("\\", $routeMatch->getParam("controller"))[2];
            $actionName = $routeMatch->getParam("action");
            $actionName = str_replace("-", "", lcfirst(ucwords($actionName, "-")));
            $moduleName = explode("\\", $routeMatch->getParam("controller"))[0];
            if (($controllerName !== "UserController" && $actionName !== "index") ||
                ($controllerName !== "UserController" && $actionName !== "logout")) {
                $result = $this->getLoginService()->filterAccess(
                    $moduleName,
                    $controllerName,
                    $actionName,
                    $taskName = "*"
                );

                if ($result == LoginService::AUTH_REQUIRED) {
                    $routeMatch->setParam("controller", UserController::class);
                    $routeMatch->setParam("action", "index");
                }

                if ($result == LoginService::ACCESS_DENIED) {
                    $routeMatch->setParam("controller", UserController::class);
                    $routeMatch->setParam("action", "unauthorized");
                    $e->getResponse()->setStatusCode(401);
                }
            }
        }
    }

    /**
     * @return LoginService
     */
    public function getLoginService(): LoginService
    {
        return $this->loginService;
    }

    /**
     * @param LoginService $loginService
     * @return LoginListener
     */
    public function setLoginService(LoginService $loginService): self
    {
        $this->loginService = $loginService;

        return $this;
    }
}
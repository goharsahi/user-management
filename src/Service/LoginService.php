<?php

namespace UserModule\Service;

use Exception;
use Laminas\Config\Config;
use Laminas\Session\Container;
use UserModule\Process\EmailProcess;
use UserModule\Process\GoogleLoginProcess;
use UserModule\Process\GoogleLogoutProcess;
use UserModule\Process\LoginProcess;

/**
 * Class LoginService
 * @package UserModule\Service
 */
class LoginService extends ServiceAbstract
{
    /** @var int ACCESS_GRANTED */
    const ACCESS_GRANTED = 1;

    /** @var int ACCESS_DENIED */
    const ACCESS_DENIED = 2;

    /** @var int AUTH_REQUIRED */
    const AUTH_REQUIRED = 3;

    /** @var EmailProcess $emailProcess */
    protected EmailProcess $emailProcess;

    /** @var LoginProcess $loginProcess */
    protected LoginProcess $loginProcess;

    /** @var GoogleLoginProcess $googleLoginProcess */
    protected GoogleLoginProcess $googleLoginProcess;

    /** @var GoogleLogoutProcess $googleLogoutProcess */
    protected GoogleLogoutProcess $googleLogoutProcess;

    /** @var Container $sessionContainer */
    protected Container $sessionContainer;

    /**
     * LoginService constructor.
     * @param Config $config
     * @param EmailProcess $emailProcess
     * @param LoginProcess $loginProcess
     * @param GoogleLoginProcess $googleLoginProcess
     * @param GoogleLogoutProcess $googleLogoutProcess
     * @param Container $sessionContainer
     */
    public function __construct(
        Config $config,
        EmailProcess $emailProcess,
        LoginProcess $loginProcess,
        GoogleLoginProcess $googleLoginProcess,
        GoogleLogoutProcess $googleLogoutProcess,
        Container $sessionContainer
    ) {
        parent::__construct($config);

        $this->emailProcess = $emailProcess;
        $this->loginProcess = $loginProcess;
        $this->googleLoginProcess = $googleLoginProcess;
        $this->googleLogoutProcess = $googleLogoutProcess;
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * @return EmailProcess
     */
    public function getEmailProcess(): EmailProcess
    {
        return $this->emailProcess;
    }

    /**
     * @param EmailProcess $emailProcess
     * @return LoginService
     */
    public function setEmailProcess(EmailProcess $emailProcess): self
    {
        $this->emailProcess = $emailProcess;

        return $this;
    }

    /**
     * @return LoginProcess
     */
    public function getLoginProcess()
    {
        return $this->loginProcess;
    }

    /**
     * @param LoginProcess $loginProcess
     * @return LoginService
     */
    public function setLoginProcess(LoginProcess $loginProcess): self
    {
        $this->loginProcess = $loginProcess;

        return $this;
    }

    /**
     * @return array
     */
    public function googleLogin(): array
    {
        if (isset($_GET["code"])) {
            $this->getGoogleLoginProcess()->setAuthCode($_GET["code"]);
        }

        $response = $this->getGoogleLoginProcess()->execute();

        return $response->getResponse();
    }

    /**
     * @return GoogleLoginProcess
     */
    public function getGoogleLoginProcess()
    {
        return $this->googleLoginProcess;
    }

    /**
     * @param GoogleLoginProcess $googleLoginProcess
     * @return LoginService
     */
    public function setGoogleLoginProcess(GoogleLoginProcess $googleLoginProcess): self
    {
        $this->googleLoginProcess = $googleLoginProcess;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function logout(): self
    {
        if ($this->getSessionContainer()->offsetExists("access_token")) {
            $this->getGoogleLogoutProcess()->execute();
        }
        $this->getSessionContainer()->getManager()->destroy();

        return $this;
    }

    /**
     * @return Container
     */
    public function getSessionContainer(): Container
    {
        return $this->sessionContainer;
    }

    /**
     * @param Container $sessionContainer
     * @return LoginService
     */
    public function setSessionContainer(Container $sessionContainer): self
    {
        $this->sessionContainer = $sessionContainer;

        return $this;
    }

    /**
     * @return GoogleLogoutProcess
     */
    public function getGoogleLogoutProcess(): GoogleLogoutProcess
    {
        return $this->googleLogoutProcess;
    }

    /**
     * @param GoogleLogoutProcess $googleLogoutProcess
     * @return LoginService
     */
    public function setGoogleLogoutProcess(GoogleLogoutProcess $googleLogoutProcess): self
    {
        $this->googleLogoutProcess = $googleLogoutProcess;

        return $this;
    }

    /**
     * @param $moduleName
     * @param $controllerName
     * @param $actionName
     * @param $taskName
     * @return int
     */
    public function filterAccess($moduleName, $controllerName, $actionName, $taskName)
    {
        if (!$this->isUserLoggedIn()) {
            return self::AUTH_REQUIRED;
        }

        $roleArray = [];
        if (!empty(
        $this->getConfig()->get("rbac")
        )) {
            $rbacArray = $this->getConfig()->get("rbac")->toArray();
            foreach ($rbacArray as $role => $permissions) {
                $inheritRole = null;
                foreach ($permissions as $key => $permission) {
                    $permissionArray = explode(".", $permission, 4);
                    if (substr($permissionArray[0], 0, 1) == "+") {
                        $inheritRole = substr($permissionArray[0], 1, strlen($permissionArray[0]) - 1);
                        continue;
                    }
                    $roleArray[$role][$key]["moduleName"] = $permissionArray[0] ?? "*";
                    $roleArray[$role][$key]["controllerName"] = $permissionArray[1] ?? "*";
                    $roleArray[$role][$key]["actionName"] = $permissionArray[2] ?? "*";
                    $roleArray[$role][$key]["taskName"] = $permissionArray[3] ?? "*";
                }
                if (!empty($inheritRole)) {
                    $roleArray[$role] = array_merge($roleArray[$role], $roleArray[$inheritRole]);
                }
            }
        }

        if (isset($roleArray[$this->getLoggedInUserRole()])) {
            foreach ($roleArray[$this->getLoggedInUserRole()] as $key => $permissionSet) {
                if (($permissionSet["moduleName"] == $moduleName || $permissionSet["moduleName"] == "*") &&
                    ($permissionSet["controllerName"] == $controllerName || $permissionSet["controllerName"] == "*") &&
                    ($permissionSet["actionName"] == $actionName || $permissionSet["actionName"] == "*") &&
                    ($permissionSet["taskName"] == $taskName || $permissionSet["taskName"] == "*" || $taskName == "*")
                ) {
                    return self::ACCESS_GRANTED;
                }
            }
        }

        return self::ACCESS_DENIED;
    }

    /**
     * @return bool
     */
    public function isUserLoggedIn(): bool
    {
        return $this->getSessionContainer()->offsetExists("authIdentity");
    }

    /**
     * @return string
     */
    public function getLoggedInUserRole(): string
    {
        return $this->getSessionContainer()->offsetGet("userRole");
    }

    /**
     * @return string
     */
    public function getAuthIdentity(): string
    {
        return $this->getSessionContainer()->offsetGet("authIdentity");
    }
}
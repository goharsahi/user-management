<?php

namespace UserModule\Controller;

use Exception;
use Laminas\Config\Config;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use UserModule\Form\EmailForm;
use UserModule\Form\LoginForm;
use UserModule\Module;
use UserModule\Service\LoginService;

/**
 * Class UserController
 * @package UserModule\Controller
 * @method FlashMessenger flashMessenger()
 */
class UserController extends AbstractActionController
{
    /** @var LoginService $loginService */
    protected LoginService $loginService;

    /** @var EmailForm $emailForm */
    protected EmailForm $emailForm;

    /** @var LoginForm $loginForm */
    protected LoginForm $loginForm;

    /** @var Config $config */
    protected Config $config;

    /** @var Container $sessionContainer */
    protected Container $sessionContainer;

    /**
     * UserController constructor.
     * @param Config $config
     * @param LoginService $loginService
     * @param LoginForm $loginForm
     * @param EmailForm $emailForm
     * @param Container $sessionContainer
     */
    public function __construct(
        Config $config,
        LoginService $loginService,
        LoginForm $loginForm,
        EmailForm $emailForm,
        Container $sessionContainer
    ) {
        $this->config = $config;
        $this->loginService = $loginService;
        $this->loginForm = $loginForm;
        $this->emailForm = $emailForm;
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * The login form, after valid POST, is sent to EmailProcess. The returned Response is then evaluated.
     *
     * oauth => false (It means, the user has never logged in before. Needs to provide password for authentication.
     * oauth => true (It means, the user has logged in earlier successfully.
     *
     * oauthEmail => (EMAIL) (It means, the email is already linked with Google and user can login through Google. If
     *               initial email matches the oauthEmail (additional check), user is forwarded for Google Login)
     *
     *
     * @return Response|ViewModel
     */
    public function indexAction()
    {
        if (!empty($this->params()->fromPost())) {
            $this->getEmailForm()->setData($this->params()->fromPost());
        }

        if ($this->getRequest()->isPost() && !$this->getEmailForm()->isValid()) {
            $this->flashMessenger()->addMessage(Module::MESSAGE_NOT_ALLOWED);
        }

        $result = null;
        if ($this->getRequest()->isPost() && $this->getEmailForm()->isValid()) {
            $this->getLoginService()->getEmailProcess()->setEmailForm($this->getEmailForm());
            $response = $this->getLoginService()->getEmailProcess()->execute();
            $result = $response->getResponse();
        }

        if ((!empty($result["oauthEmail"]) && $result["oauthEmail"] == $this->getEmailForm()->get("email")->getValue(
                )) || $this->getSessionContainer()->offsetExists("oauthEmail")) {
            return $this->redirect()->toRoute(Module::ROUTE_MODULE_CHILD_ACTIONS, ["action" => "googleLogin"]);
        }

        if ((!empty($result) && $result["oauth"] == false) || $this->getSessionContainer()->offsetExists("email")) {
            return $this->redirect()->toRoute(Module::ROUTE_MODULE_CHILD_ACTIONS, ["action" => "login"]);
        }

        return new ViewModel(
            [
                "title" => "Login",
                "emailForm" => $this->getEmailForm(),
                "actionUrl" => $this->url()->fromRoute(Module::ROUTE_MODULE_CHILD_ACTIONS, ["action" => "index"]),
                "googleLoginUrl" => $this->url()->fromRoute(
                    Module::ROUTE_MODULE_CHILD_ACTIONS,
                    ["action" => "googleLogin"]
                ),
            ]
        );
    }

    /**
     * @return EmailForm
     */
    public function getEmailForm(): EmailForm
    {
        return $this->emailForm;
    }

    /**
     * @param EmailForm $emailForm
     * @return UserController
     */
    public function setEmailForm(EmailForm $emailForm): self
    {
        $this->emailForm = $emailForm;

        return $this;
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
     * @return UserController
     */
    public function setLoginService(LoginService $loginService): self
    {
        $this->loginService = $loginService;

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
     * @return UserController
     */
    public function setSessionContainer(Container $sessionContainer): self
    {
        $this->sessionContainer = $sessionContainer;

        return $this;
    }

    /**
     * @return ViewModel
     */
    public function unauthorizedAction(): ViewModel
    {
        return new ViewModel([]);
    }

    /**
     * email/password is required for this login. This login form appears only if the email is not connected to Google.
     *
     * login => true (or if the session variable authIdentity exists, user is redirected to relevant page)
     * login => false (user is required to submit the password again.
     *
     * @return Response|ViewModel
     */
    public function loginAction()
    {
        if (!$this->getSessionContainer()->offsetExists("email")) {
            return $this->redirect()->toRoute(Module::ROUTE_MODULE_CHILD_ACTIONS, ["action" => "index"]);
        }

        if (!empty($this->params()->fromPost())) {
            $this->getLoginForm()->setData($this->params()->fromPost());
        }

        if ($this->getRequest()->isPost() && !$this->getLoginForm()->isValid()) {
            $this->flashMessenger()->addMessage(Module::MESSAGE_NOT_ALLOWED);
        }

        $result = null;
        if ($this->getRequest()->isPost()) {
            $this->getLoginService()->getLoginProcess()->setLoginForm($this->getLoginForm());
            $response = $this->getLoginService()->getLoginProcess()->execute();
            $result = $response->getResponse();
        }

        if (!empty($result["login"]) && !$result["login"]) {
            $this->flashMessenger()->addMessage($result["message"]);
        }

        if (!empty($result["login"]) && $result["login"] || $this->getLoginService()->isUserLoggedIn()) {
            $this->flashMessenger()->addMessage(Module::MESSAGE_LOGGED_IN);
            return $this->redirect()->toUrl($this->getConfig()->get("login_success_redirect"));
        }

        return new ViewModel(
            [
                "title" => "Login",
                "loginForm" => $this->getLoginForm(),
                "authEmail" => $this->getSessionContainer()->offsetGet("email"),
                "actionUrl" => $this->url()->fromRoute(Module::ROUTE_MODULE_CHILD_ACTIONS, ["action" => "login"]),
            ]
        );
    }

    /**
     * @return LoginForm
     */
    public function getLoginForm(): LoginForm
    {
        return $this->loginForm;
    }

    /**
     * @param LoginForm $loginForm
     * @return UserController
     */
    public function setLoginForm(LoginForm $loginForm): self
    {
        $this->loginForm = $loginForm;

        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return UserController
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function logoutAction()
    {
        $this->loginService->logout();

        return $this->redirect()->toRoute("user/actions", ["controller" => "UserController", "action" => "index"]);
    }

    /**
     * If user is already logged in already, he's redirected to user/index page. Initially, user will be redirected
     * to Google for getting an authorization-code (?code=xxxxxxxxxx). If failed to login, user will be redirected
     * to user/index page otherwise forwarded to relevant page as per login_success_redirect configuration in
     * login.local.php.
     *
     * @return Response
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function googleLoginAction(): Response
    {
        if (!$this->getLoginService()->isUserLoggedIn() && !$this->getSessionContainer()->offsetExists("oauthEmail")) {
            return $this->redirect()->toRoute(Module::ROUTE_MODULE_CHILD_ACTIONS, ["action" => "index"]);
        }
        $result = $this->getLoginService()->googleLogin();

        if ((array_key_exists("error", $result) && $result["error"] === "authentication")) {
            return $this->redirect()->toUrl($result["url"]);
        }

        if (array_key_exists("login", $result) && !$result["login"]) {
            $this->flashMessenger()->addMessage($result["message"]);

            return $this->redirect()->toRoute(Module::ROUTE_MODULE_CHILD_ACTIONS, ["action" => "index"]);
        }
        $this->flashMessenger()->addMessage(Module::MESSAGE_LOGGED_IN);

        return $this->redirect()->toUrl($this->getConfig()->get("login_success_redirect"));
    }
}

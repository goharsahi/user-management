<?php

namespace UserModule\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Laminas\Authentication\Adapter\DbTable\Exception\RuntimeException;
use Laminas\Config\Config;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Session\Container;
use UserModule\Form\LoginForm;
use UserModule\Response\LoginResponse;

/**
 * Class LoginRequest
 * @package UserModule\Request\LoginRequest
 */
class LoginRequest extends RequestAbstract
{
    /** @var LoginResponse $loginResponse */
    protected LoginResponse $loginResponse;

    /** @var LoginForm $loginForm */
    protected LoginForm $loginForm;

    /** @var Client $httpClient */
    protected Client $httpClient;

    /** @var Container $sessionContainer */
    protected Container $sessionContainer;

    /**
     * LoginRequest constructor.
     * @param LoginResponse $loginResponse
     * @param LoginForm $loginForm
     * @param Client $httpClient
     * @param Container $sessionContainer
     * @param Config $config
     */
    public function __construct(
        LoginResponse $loginResponse,
        LoginForm $loginForm,
        Client $httpClient,
        Container $sessionContainer,
        Config $config
    ) {
        $this->loginResponse = $loginResponse;
        $this->loginForm = $loginForm;
        $this->httpClient = $httpClient;
        $this->sessionContainer = $sessionContainer;
        $this->config = $config;
    }

    /**
     * The POST endpoint /authenticate (with email/password) should return something like below responses.
     *
     * Success:
     * {
     *   "token": "(SOME-RANDOM-TOKEN)",
     *   "exp": (TIMESTAMP INT VALUE),
     *   "hasRetailFinance": (true/false),
     *   "hasRetailFinanceZeroPercent": (true/false),
     *   "hasLoansWarehouse": (true/false),
     *   "userRole": "(USER-ROLE)", (Can be either of ADMIN/USER/GUEST)
     *   "oauthEmail": "(EMAIL)"
     * }
     * Failure:
     * {
     *   "status": "ERROR",
     *   "message": "Could not authenticate the user (EMAIL)."
     * }
     *
     * Criteria:
     * The username/id/email will be looked for in the master user table and a response needs to be sent in above
     * mentioned pattern. Success response can be any response depending upon original logic, but last two items
     * (userRole and oauthEmail) are MUST. This oauthEmail and userRole will be stored in session.
     *
     * Session Variable(s):
     *   authIdentity (email/oauthEmail after authentication for displaying current logged-in user. Also used in
     *                LoginService::isUserLoggedIn() and LoginService::getAuthIdentity() methods.)
     *   userRole (userRole linked with current logged in email/oauthEmail/authIdentity)
     *
     * Response:
     *   login => false (failed to authenticate the email. Failure message added.)
     *   login => true (successfully authenticated the email. oauthEmail also sent as response item.)
     *
     * @return LoginResponse
     * @throws RuntimeException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function send(): LoginResponse
    {
        $headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/x-www-form-urlencoded",
        ];
        $options = [
            "form_params" => [
                "email" => $this->getSessionContainer()->offsetGet("email"),
                "password" => $this->getLoginForm()->get("password")->getValue(),
            ],
        ];
        $request = new Request("POST", $this->getConfig()->custom_auth_api_uris["authenticate_uri"], $headers);
        $response = $this->getHttpClient()->sendAsync($request, $options)->wait();

        $result = json_decode($response->getBody()->getContents());

        if (!empty($result->status) && $result->status == "ERROR") {
            $this->getLoginResponse()->setResponse(["login" => false, "message" => $result->message]);

            return $this->getLoginResponse();
        }

        $this->getSessionContainer()->offsetSet("authIdentity", $this->getSessionContainer()->offsetGet("email"));
        $this->getSessionContainer()->offsetSet("userRole", $result->userRole);

        $this->getLoginResponse()->setResponse(["login" => true, "oauthEmail" => $result->oauthEmail]);

        return $this->getLoginResponse();
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
     * @return LoginRequest
     */
    public function setSessionContainer(Container $sessionContainer): self
    {
        $this->sessionContainer = $sessionContainer;

        return $this;
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
     * @return LoginRequest
     */
    public function setLoginForm(LoginForm $loginForm): self
    {
        $this->loginForm = $loginForm;

        return $this;
    }

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * @param Client $httpClient
     * @return LoginRequest
     */
    public function setHttpClient(Client $httpClient): self
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @return LoginResponse
     */
    public function getLoginResponse(): LoginResponse
    {
        return $this->loginResponse;
    }

    /**
     * @param LoginResponse $loginResponse
     * @return LoginRequest
     */
    public function setLoginResponse(LoginResponse $loginResponse): self
    {
        $this->loginResponse = $loginResponse;

        return $this;
    }
}

<?php

namespace UserModule\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Laminas\Config\Config;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Session\Container;
use UserModule\Response\GoogleLogoutResponse;
use UserModule\Service\GoogleClientService;

/**
 * Class GoogleLogoutRequest
 * @package UserModule\Request\GoogleLogoutRequest
 */
class GoogleLogoutRequest extends RequestAbstract
{
    /** @var GoogleLogoutResponse $googleLogoutResponse */
    protected GoogleLogoutResponse $googleLogoutResponse;

    /** @var GoogleClientService $googleClientService */
    protected GoogleClientService $googleClientService;

    /** @var Container $sessionContainer */
    protected Container $sessionContainer;

    /** @var string|null $authCode */
    protected ?string $authCode;

    /** @var Client $httpClient */
    protected Client $httpClient;

    /**
     * GoogleLogoutRequest constructor.
     * @param GoogleClientService $googleClientService
     * @param GoogleLogoutResponse $googleLogoutResponse
     * @param Container $sessionContainer
     * @param Client $httpClient
     * @param Config $config
     */
    public function __construct(
        GoogleClientService $googleClientService,
        GoogleLogoutResponse $googleLogoutResponse,
        Container $sessionContainer,
        Client $httpClient,
        Config $config
    ) {
        $this->googleLogoutResponse = $googleLogoutResponse;
        $this->googleClientService = $googleClientService;
        $this->sessionContainer = $sessionContainer;
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * The POST endpoint /client/1 (with client_id/client_secret from config file login.local.php) should below response.
     *
     * Success:
     * {
     *    "result": true,
     *    "client_id": "(CLIENT_ID FROM GOOGLE CLOUD CONSOLE)",
     *    "client_secret": "(CLIENT_SECRET FROM GOOGLE CLOUD CONSOLE)",
     *    "redirect_uri": "(PORTAL_CDN)/user/googleLogin",
     *    "scope": "openid email profile"
     * }
     * Failure:
     * {
     *   "status": "ERROR",
     *   "message": "Could not fetch Client Id data."
     * }
     *
     * Criteria:
     * The client_id/client_secret will be looked for in the oauth_clients table and a response in the above
     * mentioned pattern will be returned on Success. The relevant data will be inserted (first-time only)
     * through migration file in db/migrations directory (db/migrations/20220621073931_oauth_clients_insert_google_client.php).
     * Client_id will then be authenticated and upon successful authentication, user will be logged-out and
     * access_token will be revoked.
     *
     *
     * Response:
     *   logout => true (successfully logged out.)
     *
     * @return GoogleLogoutResponse
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function send(): GoogleLogoutResponse
    {
        $clientData = [
            "client_id" => $this->getConfig()->login_credentials["client_id"],
            "client_secret" => $this->getConfig()->login_credentials["client_secret"],
        ];
        $headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/x-www-form-urlencoded",
        ];
        $options = [
            "form_params" => $clientData,
        ];
        $request = new Request("POST", $this->getConfig()->custom_auth_api_uris["client_uri"], $headers);
        $response = $this->getHttpClient()->sendAsync($request, $options)->wait();

        $oauthClient = json_decode($response->getBody()->getContents());

        if (!empty($oauthClient->error)) {
            $this->getGoogleLogoutResponse()->setResponse($oauthClient->error->message);

            return $this->getGoogleLogoutResponse();
        }

        $this->getGoogleClientService()->setClientId($oauthClient->client_id);
        $this->getGoogleClientService()->setClientSecret($oauthClient->client_secret);
        $this->getGoogleClientService()->setRedirectUri($oauthClient->redirect_uri);
        $this->getGoogleClientService()->setScope($oauthClient->scope);

        $this->getGoogleClientService()->setupClient();
        $googleClient = $this->getGoogleClientService()->authenticateClient();

        if (!$googleClient) {
            $body = ["error" => "unauthenticated",];
            $this->getGoogleLogoutResponse()->setResponse($body);

            return $this->getGoogleLogoutResponse();
        }

        if (!is_array($googleClient->getAccessToken())) {
            $this->getGoogleLogoutResponse()->setResponse(["logout" => true, "message" => "Incorrect token data."]);

            return $this->getGoogleLogoutResponse();
        }

        if ($this->getSessionContainer()->offsetExists("access_token")) {
            $googleClient->revokeToken($this->getSessionContainer()->offsetGet("access_token"));
        }

        $this->getGoogleLogoutResponse()->setResponse(["logout" => true]);

        return $this->getGoogleLogoutResponse();
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
     * @return GoogleLogoutRequest
     */
    public function setHttpClient(Client $httpClient): self
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return GoogleLogoutResponse
     */
    public function getGoogleLogoutResponse(): GoogleLogoutResponse
    {
        return $this->googleLogoutResponse;
    }

    /**
     * @param GoogleLogoutResponse $googleLogoutResponse
     * @return GoogleLogoutRequest
     */
    public function setGoogleLogoutResponse(GoogleLogoutResponse $googleLogoutResponse): self
    {
        $this->googleLogoutResponse = $googleLogoutResponse;

        return $this;
    }

    /**
     * @return GoogleClientService
     */
    public function getGoogleClientService(): GoogleClientService
    {
        return $this->googleClientService;
    }

    /**
     * @param GoogleClientService $googleClientService
     * @return GoogleLogoutRequest
     */
    public function setGoogleClientService(GoogleClientService $googleClientService): self
    {
        $this->googleClientService = $googleClientService;

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
     * @return GoogleLogoutRequest
     */
    public function setSessionContainer(Container $sessionContainer): self
    {
        $this->sessionContainer = $sessionContainer;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthCode(): ?string
    {
        return $this->authCode ?? null;
    }

    /**
     * @param string|null $authCode
     * @return GoogleLogoutRequest
     */
    public function setAuthCode(?string $authCode = null): self
    {
        $this->authCode = $authCode;

        return $this;
    }
}

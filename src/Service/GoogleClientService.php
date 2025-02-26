<?php

namespace UserModule\Service;

use Laminas\Session\Container;
use UserModule\Module;
use Google_Client;

/**
 * Class GoogleClientService
 * @package CalendarModule\Service
 */
class GoogleClientService
{
    /** @var array SCOPES */
    public const SCOPES = ["openid email"];
    //NB: email and profile pic can be acquired using this endpoint with Guzzle Http : https://www.googleapis.com/oauth2/v3/userinfo?access_token=

    /** @var string ACCESS_TYPE */
    public const ACCESS_TYPE = "offline";

    /** @var bool CACHE_TIME_TO_LIVE */
    public const CACHE_TIME_TO_LIVE = 604800;

    /** @var Google_Client $googleClient */
    protected Google_Client $googleClient;

    /** @var string|null $authCode */
    protected ?string $authCode;

    /** @var string $clientId */
    protected string $clientId;

    /** @var string $clientSecret */
    protected string $clientSecret;

    /** @var string $redirectUri */
    protected string $redirectUri;

    /** @var string $scope */
    protected string $scope;

    /** @var Container $sessionContainer */
    protected Container $sessionContainer;

    /**
     * GoogleClientService constructor.
     * @param Google_Client $googleClient
     * @param Container $sessionContainer
     */
    public function __construct(
        Google_Client $googleClient,
        Container $sessionContainer
    ) {
        $this->googleClient = $googleClient;
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * @return GoogleClientService
     */
    public function setupClient(): self
    {
        $this->getGoogleClient()->setApplicationName(Module::MODULE_NAME);
        $this->getGoogleClient()->setScopes($this->getScope());
        $this->getGoogleClient()->setClientId($this->getClientId());
        $this->getGoogleClient()->setClientSecret($this->getClientSecret());
        $this->getGoogleClient()->setRedirectUri($this->getRedirectUri());
        $this->getGoogleClient()->setAccessType(self::ACCESS_TYPE);

        return $this;
    }

    /**
     * @return Google_Client
     */
    public function getGoogleClient(): Google_Client
    {
        return $this->googleClient;
    }

    /**
     * @param Google_Client $googleClient
     * @return GoogleClientService
     */
    public function setGoogleClient(Google_Client $googleClient): self
    {
        $this->googleClient = $googleClient;

        return $this;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return GoogleClientService
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return GoogleClientService
     */
    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return GoogleClientService
     */
    public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     * @return GoogleClientService
     */
    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * @return Google_Client|null
     */
    public function authenticateClient(): ?Google_Client
    {
        if ($this->getSessionContainer()->offsetExists("access_token")) {
            $accessToken = $this->getSessionContainer()->offsetGet("access_token");

            if (empty($accessToken) || (!empty($accessToken["error"]) && $accessToken["error"] == "invalid_grant")) {
                $this->getSessionContainer()->offsetUnset("access_token");
            } else {
                $this->getGoogleClient()->setAccessToken($accessToken);
            }
        }
        if ($this->getGoogleClient()->isAccessTokenExpired()) {
            $tokenCallback = function ($accessToken) {
            };
            $this->getGoogleClient()->setTokenCallback($tokenCallback);
            //NB: Fresh request for Access Token will be sent for each login attempt using Authorization Code.
            //In order to use Refresh Token to fetch Access Token, de-comment below code block.
            /*            $refreshToken = $this->getSessionContainer()
                                ->offsetGet("access_token")["refresh_token"] ?? $this->getGoogleClient()
                                ->getRefreshToken();*/
            if (!empty($refreshToken)) {
                $this->getGoogleClient()->fetchAccessTokenWithRefreshToken($refreshToken);
                $accessToken = $this->getGoogleClient()->getAccessToken();
                $this->getSessionContainer()->offsetSet("access_token", $accessToken);
            } else {
                if (!$this->getAuthCode()) {
                    return null;
                }
                $accessToken = $this->getGoogleClient()->fetchAccessTokenWithAuthCode($this->getAuthCode());
                if (empty($accessToken) || (!empty($accessToken["error"]) && $accessToken["error"] == "invalid_grant")) {
                    if ($this->getSessionContainer()->offsetExists("access_token")) {
                        $this->getSessionContainer()->offsetUnset("access_token");
                    }
                } else {
                    $this->getSessionContainer()->offsetSet("access_token", $accessToken);
                    $this->getGoogleClient()->setAccessToken($accessToken);
                }
            }
        }

        return $this->getGoogleClient();
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
     * @return GoogleClientService
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
     * @return GoogleClientService
     */
    public function setAuthCode(?string $authCode = null): self
    {
        $this->authCode = $authCode;

        return $this;
    }
}
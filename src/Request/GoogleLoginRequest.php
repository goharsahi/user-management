<?php

namespace UserModule\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Laminas\Config\Config;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Session\Container;
use UserModule\Response\GoogleLoginResponse;
use UserModule\Service\GoogleClientService;

/**
 * Class GoogleLoginRequest
 * @package UserModule\Request\GoogleLoginRequest
 */
class GoogleLoginRequest extends RequestAbstract
{
    /** @var GoogleLoginResponse $googleLoginResponse */
    protected GoogleLoginResponse $googleLoginResponse;

    /** @var GoogleClientService $googleClientService */
    protected GoogleClientService $googleClientService;

    /** @var Container $sessionContainer */
    protected Container $sessionContainer;

    /** @var string|null $authCode */
    protected ?string $authCode;

    /** @var Client $httpClient */
    protected Client $httpClient;

    /**
     * GoogleLoginRequest constructor.
     * @param GoogleClientService $googleClientService
     * @param GoogleLoginResponse $googleLoginResponse
     * @param Container $sessionContainer
     * @param Client $httpClient
     * @param Config $config
     */
    public function __construct(
        GoogleClientService $googleClientService,
        GoogleLoginResponse $googleLoginResponse,
        Container $sessionContainer,
        Client $httpClient,
        Config $config
    ) {
        $this->googleLoginResponse = $googleLoginResponse;
        $this->googleClientService = $googleClientService;
        $this->sessionContainer = $sessionContainer;
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * The POST endpoint /client/1 (with client_id/client_secret from config file login.local.php) should below response.
     *****************************************************************************************************************
     * IMPORTANT!!! redirect_uri needs to be http://localhost for testing purpose as nothing is acceptable other than
     *              localhost or a CDN.
     *****************************************************************************************************************
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
     * through migration file in db/migrations directory
     * (db/migrations/20220621073931_oauth_clients_insert_google_client.php).
     *
     *****************************************************************************************************************
     * IMPORTANT!!! redirect_uri needs to be http://localhost for testing purpose as nothing is acceptable other than
     *              localhost or a CDN.
     *****************************************************************************************************************
     * Client_id will then be authenticated and upon successful authentication, user will be logged-in.
     * Relevant data will be insert in oauth tables as below.
     *
     * Authorization Code => oauth_authorization_codes (through POST endpoint /authorization-code),
     * Success
     * {
     *    "result": true,
     *    "id": "(AUTHORIZATION-CODE)"
     * }
     * Failure:
     * {
     *   "status": "ERROR",
     *   "message": "Could not insert authorization code."
     * }
     *
     * Access Token => oauth_access_tokens (through POST endpoint /access_token),
     * Success
     * {
     *    "result": true,
     *    "id": "(ACCESS-TOKEN)"
     * }
     * Failure:
     * {
     *   "status": "ERROR",
     *   "message": "Could not insert access token."
     * }
     *
     * User Data => oauth_users (through POST endpoint /user)
     * Success
     * {
     *    "result": true,
     *    "id": "(EMAIL)"
     * }
     * Failure:
     * {
     *   "status": "ERROR",
     *   "message": "Could not insert user."
     * }
     *
     *
     * Response:
     *   login => false (failed to authenticate the Google email. Failure message added.)
     *   login => true (successfully authenticated the Google email.)
     *
     * @return GoogleLoginResponse
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function send(): GoogleLoginResponse
    {
        if (!empty($this->getAuthCode())) {
            $this->getGoogleClientService()->setAuthCode($this->getAuthCode());
        }

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
            $this->getGoogleLoginResponse()->setResponse($oauthClient->error->message);

            return $this->getGoogleLoginResponse();
        }

        $this->getGoogleClientService()->setClientId($oauthClient->client_id);
        $this->getGoogleClientService()->setClientSecret($oauthClient->client_secret);
        $this->getGoogleClientService()->setRedirectUri($oauthClient->redirect_uri);
        $this->getGoogleClientService()->setScope($oauthClient->scope);

        $this->getGoogleClientService()->setupClient();
        $googleClient = $this->getGoogleClientService()->authenticateClient();

        if (!$googleClient) {
            $body = [
                "error" => "authentication",
                "url" => filter_var(
                    $this->getGoogleClientService()->getGoogleClient()->createAuthUrl(),
                    FILTER_SANITIZE_URL
                ),
            ];
            $this->getGoogleLoginResponse()->setResponse($body);

            return $this->getGoogleLoginResponse();
        }

        if (!is_array($googleClient->getAccessToken())) {
            $this->getGoogleLoginResponse()->setResponse(["login" => false, "message" => "Incorrect token data."]);

            return $this->getGoogleLoginResponse();
        }

        if (!array_key_exists("id_token", $googleClient->getAccessToken())) {
            $this->getGoogleLoginResponse()->setResponse(["login" => false, "message" => "Incorrect token data."]);

            return $this->getGoogleLoginResponse();
        }

        $userDetails = $googleClient->verifyIdToken($googleClient->getOAuth2Service()->getIdToken());

        if ($this->getSessionContainer()->offsetGet("email") != $userDetails["email"]) {
            $this->getGoogleLoginResponse()
                ->setResponse(
                    [
                        "login" => false,
                        "message" => "Incorrect email address. Google email address doesn't match the original email.",
                    ]
                );

            return $this->getGoogleLoginResponse();
        }

        $request = new Request(
            "GET",
            $this->getConfig()->custom_auth_api_uris["user_role_uri"] . "/" . urlencode(
                $userDetails["email"]
            )
        );
        $response = $this->getHttpClient()->sendAsync($request)->wait();

        $resultUserRole = json_decode($response->getBody()->getContents());

        if (!$resultUserRole->userRole) {
            $this->getGoogleLoginResponse()->setResponse(
                ["login" => false, "message" => "No role defined for this Email. Please provide a valid Email."]
            );

            return $this->getGoogleLoginResponse();
        }

        if ($googleClient->getOAuth2Service()->getCode()) {
            $authorizationCodeData = [
                "authorization_code" => $googleClient->getOAuth2Service()->getCode(),
                "client_id" => $googleClient->getClientId(),
                "user_id" => $userDetails["email"] ?? "",
                "redirect_uri" => $googleClient->getRedirectUri(),
                "expires" => $googleClient->getOAuth2Service()->getExpiry(),
                "scope" => $googleClient->getAccessToken()["scope"] ?? "",
                "id_token" => $googleClient->getAccessToken()["id_token"] ?? "",
            ];

            $options = ["form_params" => $authorizationCodeData,];
            $request = new Request(
                "POST", $this->getConfig()->custom_auth_api_uris["authorization_code_uri"], $headers
            );
            $response = $this->getHttpClient()->sendAsync($request, $options)->wait();

            $resultOauthAuthorizationCode = json_decode($response->getBody()->getContents());

            if (!empty($resultOauthAuthorizationCode->error)) {
                $this->getGoogleLoginResponse()->setResponse(
                    ["login" => false, "message" => $resultOauthAuthorizationCode->error->message]
                );

                return $this->getGoogleLoginResponse();
            }
        }

        if (isset($googleClient->getAccessToken()["access_token"])) {
            $accessTokenData = [
                "access_token" => $googleClient->getAccessToken()["access_token"],
                "client_id" => $googleClient->getClientId(),
                "user_id" => $userDetails["email"] ?? "",
                "expires" => $googleClient->getAccessToken()["expires_in"] ?? "",
                "created" => $googleClient->getAccessToken()["created"] ?? "",
                "scope" => $googleClient->getAccessToken()["scope"] ?? "",
            ];

            $options = [
                "form_params" => $accessTokenData,
            ];
            $request = new Request("POST", $this->getConfig()->custom_auth_api_uris["access_token_uri"], $headers);
            $response = $this->getHttpClient()->sendAsync($request, $options)->wait();

            $resultOauthAccessToken = json_decode($response->getBody()->getContents());

            if (!empty($resultOauthAccessToken->error)) {
                $this->getGoogleLoginResponse()->setResponse(
                    ["login" => false, "message" => $resultOauthAccessToken->error->message]
                );

                return $this->getGoogleLoginResponse();
            }
        }

        //NB: Fresh request for Access Token will be sent for each login attempt using Authorization Code.
        //In order to use Refresh Token to fetch Access Token, de-comment below code block.
        /*
        if (isset($googleClient->getAccessToken()["refresh_token"])) {
            $refreshTokenData = [
                "refresh_token" => $googleClient->getAccessToken()["refresh_token"],
                "client_id" => $googleClient->getClientId(),
                "user_id" => $userDetails["email"] ?? "",
                "expires" => 604800,//NB: Max lifetime is 7 days after 1st use. If never used, 6 months.
                "scope" => $googleClient->getAccessToken()["scope"] ?? "",
            ];

            $options = [
                "form_params" => $refreshTokenData,
            ];
            $request = new Request("POST", $this->getConfig()->custom_auth_api_uris["refresh_token_uri"], $headers);
            $response = $this->getHttpClient()->sendAsync($request, $options)->wait();

            $resultOauthRefreshToken = json_decode($response->getBody()->getContents());
        }
        */

        $userData = [
            "email" => $userDetails["email"] ?? "",
            "first_name" => $userDetails["given_name"] ?? "",
            "last_name" => $userDetails["family_name"] ?? "",
        ];

        $options = [
            "form_params" => $userData,
        ];
        $request = new Request("POST", $this->getConfig()->custom_auth_api_uris["user_uri"], $headers);
        $response = $this->getHttpClient()->sendAsync($request, $options)->wait();

        $resultUser = json_decode($response->getBody()->getContents());

        if (!empty($resultUser->error)) {
            $this->getGoogleLoginResponse()->setResponse(["login" => false, "message" => $resultUser->error->message]);

            return $this->getGoogleLoginResponse();
        }

        $this->getSessionContainer()->offsetSet("authIdentity", $userDetails["email"]);
        $this->getSessionContainer()->offsetSet("oauthEmail", $userDetails["email"]);
        $this->getSessionContainer()->offsetSet("userRole", $resultUserRole->userRole);

        $this->getGoogleLoginResponse()->setResponse(["login" => true, "user_details" => $userDetails]);

        return $this->getGoogleLoginResponse();
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
     * @return GoogleLoginRequest
     */
    public function setAuthCode(?string $authCode = null): self
    {
        $this->authCode = $authCode;

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
     * @return GoogleLoginRequest
     */
    public function setGoogleClientService(GoogleClientService $googleClientService): self
    {
        $this->googleClientService = $googleClientService;

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
     * @return GoogleLoginRequest
     */
    public function setHttpClient(Client $httpClient): self
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return GoogleLoginResponse
     */
    public function getGoogleLoginResponse(): GoogleLoginResponse
    {
        return $this->googleLoginResponse;
    }

    /**
     * @param GoogleLoginResponse $googleLoginResponse
     * @return GoogleLoginRequest
     */
    public function setGoogleLoginResponse(GoogleLoginResponse $googleLoginResponse): self
    {
        $this->googleLoginResponse = $googleLoginResponse;

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
     * @return GoogleLoginRequest
     */
    public function setSessionContainer(Container $sessionContainer): self
    {
        $this->sessionContainer = $sessionContainer;

        return $this;
    }
}

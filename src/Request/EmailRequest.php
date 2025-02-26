<?php

namespace UserModule\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Laminas\Config\Config;
use Laminas\Session\Container;
use UserModule\Form\EmailForm;
use UserModule\Response\EmailResponse;

/**
 * Class EmailRequest
 * @package UserModule\Request
 */
class EmailRequest extends RequestAbstract
{
    /** @var EmailForm $emailForm */
    protected EmailForm $emailForm;

    /** @var EmailResponse $emailResponse */
    protected EmailResponse $emailResponse;

    /** @var Client $httpClient */
    protected Client $httpClient;

    /** @var Container $sessionContainer */
    protected Container $sessionContainer;

    /**
     * EmailRequest constructor.
     * @param EmailResponse $emailResponse
     * @param EmailForm $emailForm
     * @param Client $httpClient
     * @param Container $sessionContainer
     * @param Config $config
     */
    public function __construct(
        EmailResponse $emailResponse,
        EmailForm $emailForm,
        Client $httpClient,
        Container $sessionContainer,
        Config $config
    ) {
        $this->emailForm = $emailForm;
        $this->emailResponse = $emailResponse;
        $this->httpClient = $httpClient;
        $this->sessionContainer = $sessionContainer;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     *
     * The GET endpoint /user/<email> will return below responses.
     *
     * Success:
     * {
     *   "result": true,
     *   "id": "(EMAIL)"
     * }
     * Failure:
     * {
     *   "status": "ERROR",
     *   "message": "Could not fetch user."
     * }
     *
     * Criteria:
     * The username/id/email will be looked for in oauth_users table. If found, the user will be redirected for
     * googleLogin, otherwise, the user will be redirected and asked to enter a password provided by the admin.
     *
     * Session Variable(s):
     *   email (Email provided for login for the first time)
     *   oauthEmail (Email has been successfully linked with Google. If empty, Google Login link will be displayed for linking)
     *
     * Response:
     *   oauth => false (email doesn't exist in oauth_users table and not linked with Google)
     *   oauth => true (email exists in oauth_users table and already linked with Google)
     *
     * @return EmailResponse
     */
    public function send(): EmailResponse
    {
        $this->getSessionContainer()->offsetSet("email", $this->getEmailForm()->get("email")->getValue());
        $request = new Request(
            "GET",
            $this->getConfig()->custom_auth_api_uris["user_uri"] . "/" . urlencode(
                $this->getEmailForm()->get("email")->getValue()
            )
        );
        $response = $this->getHttpClient()->sendAsync($request)->wait();

        $result = json_decode($response->getBody()->getContents());

        if (!empty($result->status) && $result->status == "ERROR") {
            $this->getEmailResponse()->setResponse(["oauth" => false, "message" => $result->message]);

            return $this->getEmailResponse();
        }

        $this->getSessionContainer()->offsetSet("oauthEmail", $result->id);
        $this->getEmailResponse()->setResponse(["oauth" => $result, "oauthEmail" => $result->id]);

        return $this->getEmailResponse();
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
     * @return EmailRequest
     */
    public function setSessionContainer(Container $sessionContainer): self
    {
        $this->sessionContainer = $sessionContainer;

        return $this;
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
     * @return EmailRequest
     */
    public function setEmailForm(EmailForm $emailForm): self
    {
        $this->emailForm = $emailForm;

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
     * @return EmailRequest
     */
    public function setHttpClient(Client $httpClient): self
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @return EmailResponse
     */
    public function getEmailResponse(): EmailResponse
    {
        return $this->emailResponse;
    }

    /**
     * @param EmailResponse $emailResponse
     * @return EmailRequest
     */
    public function setEmailResponse(EmailResponse $emailResponse): self
    {
        $this->emailResponse = $emailResponse;

        return $this;
    }
}
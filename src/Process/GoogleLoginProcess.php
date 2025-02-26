<?php

namespace UserModule\Process;

use Laminas\Db\Sql\Exception\InvalidArgumentException;
use UserModule\Request\GoogleLoginRequest;
use UserModule\Response\GoogleLoginResponse;

/**
 * Class GoogleLoginProcess
 * @package UserModule\Process
 */
class GoogleLoginProcess extends ProcessAbstract
{
    /** @var string|null $authCode */
    protected ?string $authCode;

    /** @var GoogleLoginRequest $googleLoginRequest */
    protected GoogleLoginRequest $googleLoginRequest;

    /**
     * GoogleLoginProcess constructor.
     * @param GoogleLoginRequest $googleLoginRequest
     */
    public function __construct(GoogleLoginRequest $googleLoginRequest)
    {
        $this->googleLoginRequest = $googleLoginRequest;

        parent::__construct($googleLoginRequest);
    }

    /**
     * @return GoogleLoginResponse
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function execute(): GoogleLoginResponse
    {
        $this->getGoogleLoginRequest()->setAuthCode($this->getAuthCode());

        return $this->getGoogleLoginRequest()->send();
    }

    /**
     * @return GoogleLoginRequest
     */
    public function getGoogleLoginRequest(): GoogleLoginRequest
    {
        return $this->googleLoginRequest;
    }

    /**
     * @param GoogleLoginRequest $googleLoginRequest
     * @return GoogleLoginProcess
     */
    public function setGoogleLoginRequest(GoogleLoginRequest $googleLoginRequest): self
    {
        $this->googleLoginRequest = $googleLoginRequest;

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
     * @return GoogleLoginProcess
     */
    public function setAuthCode(?string $authCode = null): self
    {
        $this->authCode = $authCode;

        return $this;
    }
}
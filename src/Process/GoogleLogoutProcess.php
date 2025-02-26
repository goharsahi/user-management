<?php

namespace UserModule\Process;

use Laminas\Db\Sql\Exception\InvalidArgumentException;
use UserModule\Request\GoogleLogoutRequest;
use UserModule\Response\GoogleLogoutResponse;

/**
 * Class GoogleLogoutProcess
 * @package UserModule\Process
 */
class GoogleLogoutProcess extends ProcessAbstract
{
    /** @var GoogleLogoutRequest $googleLogoutRequest */
    protected GoogleLogoutRequest $googleLogoutRequest;

    /**
     * GoogleLogoutProcess constructor.
     * @param GoogleLogoutRequest $googleLogoutRequest
     */
    public function __construct(GoogleLogoutRequest $googleLogoutRequest)
    {
        $this->googleLogoutRequest = $googleLogoutRequest;

        parent::__construct($googleLogoutRequest);
    }

    /**
     * @return GoogleLogoutResponse
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function execute(): GoogleLogoutResponse
    {
        return $this->getGoogleLogoutRequest()->send();
    }

    /**
     * @return GoogleLogoutRequest
     */
    public function getGoogleLogoutRequest(): GoogleLogoutRequest
    {
        return $this->googleLogoutRequest;
    }

    /**
     * @param GoogleLogoutRequest $googleLogoutRequest
     * @return GoogleLogoutProcess
     */
    public function setGoogleLogoutRequest(GoogleLogoutRequest $googleLogoutRequest): self
    {
        $this->googleLogoutRequest = $googleLogoutRequest;

        return $this;
    }
}
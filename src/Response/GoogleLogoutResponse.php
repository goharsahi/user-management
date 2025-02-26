<?php

namespace UserModule\Response;

/**
 * Class GoogleLogoutResponse
 * @package GoogleLoginModule\Response\UserResponse
 */
class GoogleLogoutResponse extends ResponseAbstract
{
    /** @var array $response */
    protected array $response;

    /** @inheritdoc */
    public function populate(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @param array $response
     * @return GoogleLogoutResponse
     */
    public function setResponse($response): self
    {
        $this->response = $response;

        return $this;
    }
}

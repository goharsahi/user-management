<?php

namespace UserModule\Response;

/**
 * Class GoogleLoginResponse
 * @package GoogleLoginModule\Response\UserResponse
 */
class GoogleLoginResponse extends ResponseAbstract
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
     * @return GoogleLoginResponse
     */
    public function setResponse($response): self
    {
        $this->response = $response;

        return $this;
    }
}

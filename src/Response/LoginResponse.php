<?php

namespace UserModule\Response;

/**
 * Class LoginResponse
 * @package LoginModule\Response\UserResponse
 */
class LoginResponse extends ResponseAbstract
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
     * @return LoginResponse
     */
    public function setResponse($response): self
    {
        $this->response = $response;

        return $this;
    }
}

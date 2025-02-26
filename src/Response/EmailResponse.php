<?php

namespace UserModule\Response;

/**
 * Class EmailResponse
 * @package EmailModule\Response\UserResponse
 */
class EmailResponse extends ResponseAbstract
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
     * @return EmailResponse
     */
    public function setResponse($response): self
    {
        $this->response = $response;

        return $this;
    }
}

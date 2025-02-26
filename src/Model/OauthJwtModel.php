<?php

namespace UserModule\Model;

/**
 * Class OauthJwtModel
 * @package UserModule\Model
 */
class OauthJwtModel extends ModelAbstract
{
    /** @var string $client_id */
    protected string $client_id;

    /** @var string $subject */
    protected string $subject;

    /** @var string $public_key */
    protected string $public_key;

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->client_id;
    }

    /**
     * @param string $clientId
     * @return OauthJwtModel
     */
    public function setClientId(string $clientId): self
    {
        $this->client_id = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return OauthJwtModel
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->public_key;
    }

    /**
     * @param string $publicKey
     * @return OauthJwtModel
     */
    public function setPublicKey(string $publicKey): self
    {
        $this->public_key = $publicKey;

        return $this;
    }
}
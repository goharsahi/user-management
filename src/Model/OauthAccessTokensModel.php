<?php

namespace UserModule\Model;

/**
 * Class OauthAccessTokensModel
 * @package UserModule\Model
 */
class OauthAccessTokensModel extends ModelAbstract
{
    /** @var string $access_token */
    protected string $access_token;

    /** @var string $client_id */
    protected string $client_id;

    /** @var string $user_id */
    protected string $user_id;

    /** @var string $expires */
    protected string $expires;

    /** @var string $scope */
    protected string $scope;

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @param string $accessToken
     * @return OauthAccessTokensModel
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->access_token = $accessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->client_id;
    }

    /**
     * @param string $clientId
     * @return OauthAccessTokensModel
     */
    public function setClientId(string $clientId): self
    {
        $this->client_id = $clientId;

        return $this;
    }

    /** @return string */
    public function getUserId(): string
    {
        return $this->user_id;
    }

    /**
     * @param string $userId
     * @return OauthAccessTokensModel
     */
    public function setUserId(string $userId): self
    {
        $this->user_id = $userId;

        return $this;
    }

    /** @return string */
    public function getExpires(): string
    {
        return $this->expires;
    }

    /**
     * @param string $expires
     * @return OauthAccessTokensModel
     */
    public function setExpires(string $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    /** @return string */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return OauthAccessTokensModel
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }
}
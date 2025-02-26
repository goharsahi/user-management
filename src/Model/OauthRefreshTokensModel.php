<?php

namespace UserModule\Model;

/**
 * Class OauthRefreshTokensModel
 * @package UserModule\Model
 */
class OauthRefreshTokensModel extends ModelAbstract
{
    /** @var string $client_id */
    protected string $client_id;

    /** @var string $user_id */
    protected string $user_id;

    /** @var string $expires */
    protected string $expires;

    /** @var string $scope */
    protected string $scope;

    /** @var string $refresh_token */
    protected string $refresh_token;

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    /**
     * @param string $refreshToken
     * @return OauthRefreshTokensModel
     */
    public function setRefreshToken(string $refreshToken): self
    {
        $this->refresh_token = $refreshToken;

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
     * @return OauthRefreshTokensModel
     */
    public function setClientId(string $clientId): self
    {
        $this->client_id = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->user_id;
    }

    /**
     * @param string $userId
     * @return OauthRefreshTokensModel
     */
    public function setUserId(string $userId): self
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpires(): string
    {
        return $this->expires;
    }

    /**
     * @param string $expires
     * @return OauthRefreshTokensModel
     */
    public function setExpires(string $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return OauthRefreshTokensModel
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }
}
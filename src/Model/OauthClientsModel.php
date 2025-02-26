<?php

namespace UserModule\Model;

/**
 * Class OauthClientsModel
 * @package UserModule\Model
 */
class OauthClientsModel extends ModelAbstract
{
    /** @var string $client_id */
    protected string $client_id;

    /** @var string $client_secret */
    protected string $client_secret;

    /** @var string $redirect_uri */
    protected string $redirect_uri;

    /** @var string $grant_types */
    protected string $grant_types;

    /** @var string $scope */
    protected string $scope;

    /** @var string $user_id */
    protected string $user_id;

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->client_id;
    }

    /**
     * @param string $clientId
     * @return OauthClientsModel
     */
    public function setClientId(string $clientId): self
    {
        $this->client_id = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->client_secret;
    }

    /**
     * @param string $clientSecret
     * @return OauthClientsModel
     */
    public function setClientSecret(string $clientSecret): self
    {
        $this->client_secret = $clientSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirect_uri;
    }

    /**
     * @param string $redirectUri
     * @return OauthClientsModel
     */
    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirect_uri = $redirectUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getGrantTypes(): string
    {
        return $this->grant_types;
    }

    /**
     * @param string $grantTypes
     * @return OauthClientsModel
     */
    public function setGrantTypes(string $grantTypes): self
    {
        $this->grant_types = $grantTypes;

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
     * @return OauthClientsModel
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

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
     * @return OauthClientsModel
     */
    public function setUserId(string $userId): self
    {
        $this->user_id = $userId;

        return $this;
    }
}
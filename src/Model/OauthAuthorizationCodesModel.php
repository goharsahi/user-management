<?php

namespace UserModule\Model;

/**
 * Class OauthAuthorizationCodesModel
 * @package UserModule\Model
 */
class OauthAuthorizationCodesModel extends ModelAbstract
{
    /** @var string $authorization_code */
    protected string $authorization_code;

    /** @var string $client_id */
    protected string $client_id;

    /** @var string $user_id */
    protected string $user_id;

    /** @var string $redirect_uri */
    protected string $redirect_uri;

    /** @var string $expires */
    protected string $expires;

    /** @var string $scope */
    protected string $scope;

    /** @var string $id_token */
    protected string $id_token;

    /**
     * @return string
     */
    public function getAuthorizationCode(): string
    {
        return $this->authorization_code;
    }

    /**
     * @param string $authorizationCode
     * @return OauthAuthorizationCodesModel
     */
    public function setAuthorizationCode(string $authorizationCode): self
    {
        $this->authorization_code = $authorizationCode;
        
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
     * @return OauthAuthorizationCodesModel
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
     * @return OauthAuthorizationCodesModel
     */
    public function setUserId(string $userId): self
    {
        $this->user_id = $userId;
        
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
     * @return OauthAuthorizationCodesModel
     */
    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirect_uri = $redirectUri;
        
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
     * @return OauthAuthorizationCodesModel
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
     * @return OauthAuthorizationCodesModel
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdToken(): string
    {
        return $this->id_token;
    }

    /**
     * @param string $idToken
     * @return OauthAuthorizationCodesModel
     */
    public function setIdToken(string $idToken): self
    {
        $this->id_token = $idToken;

        return $this;
    }
}
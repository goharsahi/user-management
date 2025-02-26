<?php

namespace UserModule\Model;

/**
 * Class OauthScopesModel
 * @package UserModule\Model
 */
class OauthScopesModel extends ModelAbstract
{
    /** @var string $type */
    protected string $type;

    /** @var string $scope */
    protected string $scope;

    /** @var string $client_id */
    protected string $client_id;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return OauthScopesModel
     */
    public function setType(string $type = "supported"): self
    {
        $this->type = $type;

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
     * @return OauthScopesModel
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

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
     * @return OauthScopesModel
     */
    public function setClientId(string $clientId): self
    {
        $this->client_id = $clientId;

        return $this;
    }
}
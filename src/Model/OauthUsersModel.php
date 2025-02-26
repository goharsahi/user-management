<?php

namespace UserModule\Model;

/**
 * Class OauthUsersModel
 * @package UserModule\Model
 */
class OauthUsersModel extends ModelAbstract
{
    /** @var string $username */
    protected string $username;

    /** @var string $password */
    protected string $password;

    /** @var string $firstName */
    protected string $firstName;

    /** @var string $lastName */
    protected string $lastName;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return OauthUsersModel
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return OauthUsersModel
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return OauthUsersModel
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return OauthUsersModel
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
}
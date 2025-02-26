<?php

namespace UserModule\Repository;

use Carbon\Carbon;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Hydrator\ReflectionHydrator;
use UserModule\Model\OauthAuthorizationCodesModel;

/**
 * Class OauthAuthorizationCodesRepository
 * @package UserModule\Repository
 */
class OauthAuthorizationCodesRepository extends RepositoryAbstract
{
    /**
     * OauthAuthorizationCodesRepository constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct($dbAdapter);
    }

    /**
     * @param string $clientId
     * @return object|null
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function findOauthAuthorizationCodesByClientId(string $clientId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_authorization_codes')
            ->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthAuthorizationCodesModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }

    /**
     * @param array $data
     * @return bool
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function upsert(array $data): bool
    {
        $sql = $this->getSql();
        $oauthAuthorizationCodeModel = null;

        if (!empty($data) && isset($data["user_id"]) && isset($data["client_id"])) {
            $oauthAuthorizationCodeModel = $this->findOauthAuthorizationCodesByUserIdAndClientId(
                $data["user_id"],
                $data["client_id"]
            );
        }

        if (!$oauthAuthorizationCodeModel) {
            $select = $sql
                ->insert('oauth_authorization_codes')
                ->columns(
                    ["authorization_code", "client_id", "user_id", "redirect_uri", "expires", "scope", "id_token"]
                )
                ->values(
                    [
                        "authorization_code" => $data["authorization_code"],
                        "client_id" => $data["client_id"],
                        "user_id" => $data["user_id"],
                        "redirect_uri" => $data["redirect_uri"],
                        "expires" => Carbon::now()
                            ->addSeconds($data["expires"]),
                        "scope" => $data["scope"],
                        "id_token" => $data["id_token"],
                    ]
                );
            $statement = $sql->prepareStatementForSqlObject($select);
            $statement->execute();

            return true;
        }

        $previousData = $this->findOauthAuthorizationCodesByAuthorizationCode($data["authorization_code"]);
        $newData = [
            "authorization_code" => $data["authorization_code"],
            "redirect_uri" => $data["redirect_uri"],
            "scope" => $data["scope"],
            "id_token" => $data["id_token"],
        ];
        if (isset($previousData) && $previousData->getAuthorizationCode() != $data["authorization_code"]) {
            $newData["expires"] = Carbon::now()
                ->addSeconds($data["expires"]);
        }

        $select = $sql->update('oauth_authorization_codes')
            ->set($newData)
            ->where(["user_id" => $data["user_id"]])
            ->where(["client_id" => $data["client_id"]]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $statement->execute();

        return true;
    }

    /**
     * @param string $userId
     * @param string $clientId
     * @return object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthAuthorizationCodesByUserIdAndClientId(string $userId, string $clientId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_authorization_codes')
            ->where(["user_id" => $userId])
            ->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthAuthorizationCodesModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }

    /**
     * @param string $authorizationCode
     * @return object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthAuthorizationCodesByAuthorizationCode(string $authorizationCode): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_authorization_codes')
            ->where(["authorization_code" => $authorizationCode]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthAuthorizationCodesModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }
}

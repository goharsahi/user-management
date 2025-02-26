<?php

namespace UserModule\Repository;

use Carbon\Carbon;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Hydrator\ReflectionHydrator;
use UserModule\Model\OauthAccessTokensModel;

/**
 * Class OauthAccessTokensRepository
 * @package UserModule\Repository
 */
class OauthAccessTokensRepository extends RepositoryAbstract
{
    /**
     * OauthAccessTokensRepository constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct($dbAdapter);
    }

    /**
     * @param string $clientId
     * @return Object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthAccessTokensByClientId(string $clientId): ?Object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_access_tokens');
        $select->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthAccessTokensModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }

    /**
     * @param string $userId
     * @return Object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthAccessTokensByUserID(string $userId): ?Object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_access_tokens');
        $select->where(["user_id" => $userId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthAccessTokensModel()
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
        $oauthAccessTokensModel = null;

        if (!empty($data) && isset($data["user_id"]) && isset($data["client_id"])) {
            $oauthAccessTokensModel = $this->findOauthAccessTokensByUserIdAndClientId(
                $data["user_id"],
                $data["client_id"]
            );
        }

        if (!$oauthAccessTokensModel) {
            $select = $sql
                ->insert('oauth_access_tokens')
                ->columns(["access_token", "client_id", "user_id", "expires", "scope"])
                ->values(
                    [
                        "access_token" => $data["access_token"],
                        "client_id" => $data["client_id"],
                        "user_id" => $data["user_id"],
                        "expires" => Carbon::createFromTimestamp($data["created"])
                            ->addSeconds($data["expires"]),
                        "scope" => $data["scope"],
                    ]
                );
            $statement = $sql->prepareStatementForSqlObject($select);
            $statement->execute();

            return true;
        }

        $previousData = $this->findOauthAccessTokensByAccessToken($data["access_token"]);
        $newData = [
            "access_token" => $data["access_token"],
            "scope" => $data["scope"],
        ];
        if (isset($previousData) && $previousData->getAccessToken() != $data["access_token"]) {
            $newData["expires"] = Carbon::now()
                ->addSeconds($data["expires"]);
        }

        $select = $sql->update('oauth_access_tokens')
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
     * @return Object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthAccessTokensByUserIdAndClientId(string $userId, string $clientId): ?Object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_access_tokens')
            ->where(["user_id" => $userId])
            ->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthAccessTokensModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }

    /**
     * @param string $accessToken
     * @return Object|null
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function findOauthAccessTokensByAccessToken(string $accessToken): ?Object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_access_tokens');
        $select->where(["access_token" => $accessToken]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthAccessTokensModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }
}

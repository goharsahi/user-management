<?php

namespace UserModule\Repository;

use Carbon\Carbon;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Hydrator\ReflectionHydrator;
use UserModule\Model\OauthRefreshTokensModel;

/**
 * Class OauthRefreshTokensRepository
 * @package UserModule\Repository
 */
class OauthRefreshTokensRepository extends RepositoryAbstract
{
    /**
     * OauthRefreshTokensRepository constructor.
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
    public function findOauthRefreshTokensByClientId(string $clientId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_refresh_tokens');
        $select->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthRefreshTokensModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }

    /**
     * @param string $userId
     * @return object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthRefreshTokensByUserID(string $userId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_refresh_tokens');
        $select->where(["user_id" => $userId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthRefreshTokensModel()
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
    public function upsert(array $data)
    {
        $sql = $this->getSql();

        if (!empty($data) && isset($data["user_id"]) && isset($data["client_id"])) {
            $oauthRefreshTokensModel = $this->findOauthRefreshTokensByUserIdAndClientId(
                $data["user_id"],
                $data["client_id"]
            );
        }

        if (!$oauthRefreshTokensModel) {
            $select = $sql
                ->insert('oauth_refresh_tokens')
                ->columns(["refresh_token", "client_id", "user_id", "expires", "scope"])
                ->values(
                    [
                        "refresh_token" => $data["refresh_token"],
                        "client_id" => $data["client_id"],
                        "user_id" => $data["user_id"],
                        "expires" => Carbon::now()
                            ->addSeconds($data["expires"]),
                        "scope" => $data["scope"],
                    ]
                );
            $statement = $sql->prepareStatementForSqlObject($select);
            $statement->execute();

            return true;
        }

        $previousData = $this->findOauthRefreshTokensByRefreshToken($data["refresh_token"]);
        $newData = [
            "refresh_token" => $data["refresh_token"],
            "scope" => $data["scope"],
        ];
        if (isset($previousData) && $previousData->getRefreshToken() != $data["refresh_token"]) {
            $newData["expires"] = Carbon::now()
                ->addSeconds($data["expires"]);
        }

        $select = $sql->update('oauth_refresh_tokens')
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
    public function findOauthRefreshTokensByUserIdAndClientId(string $userId, string $clientId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_refresh_tokens')
            ->where(["user_id" => $userId])
            ->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthRefreshTokensModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }

    /**
     * @param string $refreshToken
     * @return object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthRefreshTokensByRefreshToken(string $refreshToken): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_refresh_tokens');
        $select->where(["refresh_token" => $refreshToken]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthRefreshTokensModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }
}

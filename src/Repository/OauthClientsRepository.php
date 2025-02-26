<?php

namespace UserModule\Repository;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Hydrator\ReflectionHydrator;
use UserModule\Model\OauthClientsModel;

/**
 * Class OauthClientsRepository
 * @package UserModule\Repository
 */
class OauthClientsRepository extends RepositoryAbstract
{
    /**
     * OauthClientsRepository constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct($dbAdapter);
    }

    /**
     * @param string $clientId
     * @return object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthClientsByClientId(string $clientId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_clients');
        $select->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthClientsModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }

    /**
     * @param string $userId
     * @return object|null
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function findOauthClientsByUserID(string $userId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_clients');
        $select->where(["user_id" => $userId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthClientsModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }
}

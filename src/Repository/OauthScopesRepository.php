<?php

namespace UserModule\Repository;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Hydrator\ReflectionHydrator;
use UserModule\Model\OauthScopesModel;

/**
 * Class OauthScopesRepository
 * @package UserModule\Repository
 */
class OauthScopesRepository extends RepositoryAbstract
{
    /**
     * OauthScopesRepository constructor.
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
    public function findOauthScopesByClientId(string $clientId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_scopes');
        $select->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthScopesModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }

    /**
     * @param string $scope
     * @return object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthScopesByScope(string $scope): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_scopes');
        $select->where(["scope" => $scope]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthScopesModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }
}
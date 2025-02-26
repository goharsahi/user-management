<?php

namespace UserModule\Repository;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Hydrator\ReflectionHydrator;
use UserModule\Model\OauthJwtModel;

/**
 * Class OauthJwtRepository
 * @package UserModule\Repository
 */
class OauthJwtRepository extends RepositoryAbstract
{
    /**
     * OauthJwtRepository constructor.
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
    public function findOauthJwtByClientId(string $clientId): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_jwt');
        $select->where(["client_id" => $clientId]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthJwtModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }
}

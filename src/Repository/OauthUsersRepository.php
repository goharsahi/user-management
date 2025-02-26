<?php

namespace UserModule\Repository;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Hydrator\ReflectionHydrator;
use UserModule\Model\OauthUsersModel;

/**
 * Class OauthUsersRepository
 * @package UserModule\Repository
 */
class OauthUsersRepository extends RepositoryAbstract
{
    /**
     * OauthUsersRepository constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        parent::__construct($dbAdapter);
    }

    /**
     * @param string $username
     * @return object|null
     * @throws InvalidArgumentException
     * @throws \Laminas\Db\ResultSet\Exception\InvalidArgumentException
     */
    public function findOauthUsersByUserId(string $username): ?object
    {
        $sql = $this->getSql();
        $select = $sql->select('oauth_users');
        $select->where(["username" => $username]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = null;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(
                new ReflectionHydrator(),
                new OauthUsersModel()
            );
            $resultSet->initialize($result);
        }

        return $resultSet->current();
    }
}

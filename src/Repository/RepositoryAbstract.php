<?php

namespace UserModule\Repository;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Sql;

/**
 * Class RepositoryAbstract
 * @package UserModule\Repository
 */
abstract class RepositoryAbstract implements RepositoryInterface
{
    /** @var Adapter $dbAdapter NB: no typehint because of mock in unit testing */
    protected $dbAdapter;

    /** @var Sql $sql */
    protected $sql;

    /**
     * RepositoryAbstract constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        $this->sql = new Sql($this->getDbAdapter());
    }

    /**
     * NB: Adapter return typehint deliberately omitted for unit tests
     *
     * @return Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * NB: Adapter typehint deliberately omitted for unit tests
     *
     * @param Adapter $dbAdapter
     * @return $this
     */
    public function setDbAdapter($dbAdapter): self
    {
        $this->dbAdapter = $dbAdapter;

        return $this;
    }

    /**
     * @return Sql
     */
    public function getSql(): Sql
    {
        return $this->sql;
    }

    /**
     * @param Sql $sql
     * @return RepositoryAbstract
     */
    public function setSql(Sql $sql): self
    {
        $this->sql = $sql;

        return $this;
    }
}
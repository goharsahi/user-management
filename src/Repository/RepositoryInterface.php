<?php

namespace UserModule\Repository;

use Laminas\Db\Adapter\Adapter;

/**
 * Interface RepositoryInterface
 * @package UserModule\Repository
 */
interface RepositoryInterface
{
    /**
     * NB: Adapter typehint deliberately omitted for unit tests
     *
     * @return Adapter
     */
    public function getDbAdapter();

    /**
     * NB: Adapter typehint deliberately omitted for unit tests
     *
     * @param Adapter $dbAdapter
     * @return $this
     */
    public function setDbAdapter($dbAdapter): self;
}
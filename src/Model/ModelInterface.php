<?php

namespace UserModule\Model;

/**
 * Class ModelInterface
 * @package UserModule\Model\ModelInterface
 */
interface ModelInterface
{
    /**
     * @param array $properties
     * @return ModelInterface|array
     */
    public function exchangeArray(array $properties = []);
}

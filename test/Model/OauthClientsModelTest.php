<?php

namespace UserModuleTest\Model;

use PHPUnit\Framework\ExpectationFailedException;
use ReflectionException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use UserModule\Model\OauthClientsModel;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class OauthClientsModelTest
 * @package UserModuleTest\Model
 */
class OauthClientsModelTest extends AbstractApplicationTestCase
{
    /** @var string DEFAULT_TESTING_VALUE */
    const DEFAULT_TESTING_VALUE = "Test";

    /**
     * @param string $propertyName
     * @param string $value
     * @dataProvider allStringProperties()
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testStringGettersSetters(string $propertyName, string $value)
    {
        $modelName = new OauthClientsModel();

        if (
        method_exists($modelName, "set" . ucfirst($propertyName))
        ) {
            $getter = "get" . ucfirst($propertyName);
            $setter = "set" . ucfirst($propertyName);

            $modelName->$setter($value);

            $this->assertEquals($value, $modelName->$getter());
        }
    }

    /**
     * @return array[]
     */
    public function allStringProperties(): array
    {
        return [
            ["clientId", self::DEFAULT_TESTING_VALUE,],
            ["clientSecret", self::DEFAULT_TESTING_VALUE,],
            ["redirectUri", self::DEFAULT_TESTING_VALUE,],
            ["grantTypes", self::DEFAULT_TESTING_VALUE,],
            ["scope", self::DEFAULT_TESTING_VALUE,],
            ["userId", self::DEFAULT_TESTING_VALUE,],
        ];
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function testAllValuesAreSetAndGet()
    {
        $modelName = new OauthClientsModel();
        $allValues = [
            "clientId" => self::DEFAULT_TESTING_VALUE,
            "clientSecret" => self::DEFAULT_TESTING_VALUE,
            "redirectUri" => self::DEFAULT_TESTING_VALUE,
            "grantTypes" => self::DEFAULT_TESTING_VALUE,
            "scope" => self::DEFAULT_TESTING_VALUE,
            "userId" => self::DEFAULT_TESTING_VALUE,
        ];
        foreach ($allValues as $propertyName => $value) {
            if (method_exists($modelName, "set" . ucfirst($propertyName))) {
                $setter = "set" . ucfirst($propertyName);

                $modelName->$setter($value);
            }
        }
        $values = $modelName->exchangeArray();

        $this->assertIsArray($values);

        $modelName = new OauthClientsModel();
        $modelName->exchangeArray($values);

        $this->assertEquals($values, $modelName->getArrayCopy());
    }
}

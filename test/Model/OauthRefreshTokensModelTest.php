<?php

namespace UserModuleTest\Model;

use PHPUnit\Framework\ExpectationFailedException;
use ReflectionException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use UserModule\Model\OauthRefreshTokensModel;
use UserModuleTest\AbstractApplicationTestCase;

/**
 * Class OauthRefreshTokensModelTest
 * @package UserModuleTest\Model
 */
class OauthRefreshTokensModelTest extends AbstractApplicationTestCase
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
        $modelName = new OauthRefreshTokensModel();

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
            ["userId", self::DEFAULT_TESTING_VALUE,],
            ["expires", self::DEFAULT_TESTING_VALUE,],
            ["scope", self::DEFAULT_TESTING_VALUE,],
            ["refreshToken", self::DEFAULT_TESTING_VALUE,],
        ];
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function testAllValuesAreSetAndGet()
    {
        $modelName = new OauthRefreshTokensModel();
        $allValues = [
            "clientId" => self::DEFAULT_TESTING_VALUE,
            "userId" => self::DEFAULT_TESTING_VALUE,
            "expires" => self::DEFAULT_TESTING_VALUE,
            "scope" => self::DEFAULT_TESTING_VALUE,
            "refreshToken" => self::DEFAULT_TESTING_VALUE,
        ];
        foreach ($allValues as $propertyName => $value) {
            if (method_exists($modelName, "set" . ucfirst($propertyName))) {
                $setter = "set" . ucfirst($propertyName);

                $modelName->$setter($value);
            }
        }
        $values = $modelName->exchangeArray();

        $this->assertIsArray($values);

        $modelName = new OauthRefreshTokensModel();
        $modelName->exchangeArray($values);

        $this->assertEquals($values, $modelName->getArrayCopy());
    }
}

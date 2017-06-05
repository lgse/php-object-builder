<?php
namespace Tests\PHPOB;

use PHPOB\ObjectBuilder;
use PHPUnit\Framework\Assert;
use Tests\PHPOB\Mocks\MockClass;
use Tests\PHPOB\Mocks\MockClassWithArrayParameter;
use Tests\PHPOB\Mocks\MockClassWithoutConstructor;
use ReflectionClass;
use Tests\PHPOB\Mocks\MockClassWithoutParameters;

/**
 * Class ObjectBuilderTest
 * @package Tests\PHPOB
 * @author Pierre Bérubé <pierre@lgse.com>
 */
class ObjectBuilderTest extends Test
{
    public function testConstructorParametersAreBeingCached()
    {
        $reflection = new ReflectionClass(ObjectBuilder::class);
        $cache = $reflection->getProperty('cache');
        $cache->setAccessible(true);

        MockClass::getInstance((object) [
            'mockSubClass' => '',
            'string' => '',
        ]);

        Assert::assertArrayHasKey(MockClass::class, $cache->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testObjectsWithoutConstructorsThrowException()
    {
        $builder = new ObjectBuilder(MockClassWithoutConstructor::class);
        $builder->getObject([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNullArgumentThrowsExceptionForNonNullableOrOptionalParameter()
    {
        MockClass::getInstance([
            'mockSubClass' => null,
            'string' => '',
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArgumentWithWrongTypeThrowsException()
    {
        MockClass::getInstance([
            'mockSubClass' => '',
            'string' => 7,
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArgumentWithWrongInstanceTypeThrowsException()
    {
        MockClass::getInstance([
            'mockSubClass' => new MockClassWithoutParameters(),
            'string' => '',
        ]);
    }

    public function testGetObjectMethodReturnsObjectIfNoConstructorParametersArePresent()
    {
        $builder = new ObjectBuilder(MockClassWithoutParameters::class);
        $object = $builder->getObject([]);
        Assert::assertInstanceOf(MockClassWithoutParameters::class, $object);
    }

    public function testArrayArgumentIsBeingPassedThroughInsteadOfUsedAsArguments()
    {
        $builder = new ObjectBuilder(MockClassWithArrayParameter::class);
        $object = $builder->getObject([]);
        Assert::assertInstanceOf(MockClassWithArrayParameter::class, $object);
    }
}
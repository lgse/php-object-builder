<?php
namespace Tests\PHPOB;

use PHPUnit\Framework\Assert;
use Tests\PHPOB\Mocks\MockClass;

/**
 * Class ModelTest
 * @package Tests\PHPOB
 * @author Pierre Bérubé <pierre@lgse.com>
 */
class ModelTest extends Test
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInstanceMethodFailsWithInvalidArgumentTypeSupplied()
    {
        MockClass::getInstance(null);
    }

    public function testGetInstanceReturnsObject()
    {
        $object = MockClass::getInstance((object) [
            'mockSubClass' => '',
            'string' => '',
        ]);

        Assert::assertInstanceOf(MockClass::class, $object);
    }
}
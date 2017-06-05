<?php
namespace Tests\PHPOB\Mocks;

use PHPOB\Model;

/**
 * Class MockClass
 * @author Pierre Bérubé <pierre@lgse.com>
 */
class MockClass extends Model
{
    public function __construct(
        MockSubClass $mockSubClass,
        MockSubClass $nullableMockClass = null,
        string $string,
        int $nullable = null,
        $optional
    ) {
    }
}
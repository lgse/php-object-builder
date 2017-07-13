<?php
namespace Tests\PHPOB\Mocks;

/**
 * Class MockClassWithBooleanIntegerDefaultParameters
 * @package Tests\PHPOB\Mocks
 * @author Pierre Bérubé <pierre@lgse.com>
 */
class MockClassWithBooleanIntegerDefaultParameters
{
    public function __construct(bool $bool = false, int $int)
    {
    }
}
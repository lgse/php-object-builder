<?php
namespace Tests\PHPOB;

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Class Test
 * @package Tests\PHPOB
 * @author Pierre Bérubé <pierre@lgse.com>
 */
abstract class Test extends TestCase
{
    use MockeryPHPUnitIntegration;
}
<?php

namespace Permafrost\RayCli\Tests;

use Permafrost\RayCli\Utilities;
use PHPUnit\Framework\TestCase;

class UtilitiesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['APP_ENV'] = 'testing';
        putenv('APP_ENV=testing');
    }

    public static function setUpBeforeClass(): void
    {
        $unreadableFn = __DIR__ . '/unreadable.txt';

        touch($unreadableFn);
        touch(__DIR__ . '/empty.txt');
        chmod($unreadableFn, 0200);

        file_put_contents(__DIR__ . '/test.phar', str_repeat('#', 4096));
        clearstatcache(true, __DIR__ . '/test.phar');

        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        $unreadableFn = __DIR__ . '/unreadable.txt';

        //file_put_contents(__DIR__ . '/test.phar', '-');
        if (file_exists(__DIR__ . '/test.phar')) {
            unlink(__DIR__ . '/test.phar');
        }

        if (file_exists($unreadableFn)) {
            unlink($unreadableFn);
        }

        if (file_exists(__DIR__ . '/empty.txt')) {
            unlink(__DIR__ . '/empty.txt');
        }

        clearstatcache(true);

        parent::tearDownAfterClass();
    }

    /** @test */
    public function it_detects_when_running_unit_tests(): void
    {
        $this->assertTrue(Utilities::isRunningUnitTests());
    }

    /** @test */
    public function it_detects_when_not_running_unit_tests(): void
    {
        $_ENV['APP_ENV'] = 'not_testing';
        unset($_ENV['APP_ENV']);
        putenv('APP_ENV=');

        $this->assertFalse(Utilities::isRunningUnitTests());
    }

    /** @test */
    public function it_detects_when_running_in_a_ci_environment(): void
    {
        $_ENV['CI'] = 'true';
        putenv('CI=true');

        $this->assertEquals(getenv('CI') ? true : false, Utilities::isRunningInCI());
    }

    /** @test */
    public function it_detects_when_not_running_in_a_ci_environment(): void
    {
        $_ENV['CI'] = 'not_testing';
        unset($_ENV['CI']);

        putenv('CI=');

        $this->assertEquals(getenv('CI') ? true : false, Utilities::isRunningInCI());
    }

    /** @test */
    public function it_can_get_the_package_version(): void
    {
        $this->assertEquals('dev-main', Utilities::getPackageVersion());

        $_ENV['APP_ENV'] = 'not_testing';
        unset($_ENV['APP_ENV']);
        putenv('APP_ENV=');

        $this->assertEquals('dev-main', Utilities::getPackageVersion());
    }

    /** @test */
    public function it_detects_when_running_as_a_phar(): void
    {
        $this->assertFalse(Utilities::runningAsPhar());
        $this->assertFalse(Utilities::runningAsPhar(' '));
        $this->assertFalse(Utilities::runningAsPhar(__DIR__ . '/unreadable.txt'));
        $this->assertTrue(Utilities::runningAsPhar(__DIR__ . '/test.phar'));
        $this->assertFalse(Utilities::runningAsPhar(__DIR__ . '/empty.txt'));
    }

    /** @test */
    public function it_gets_ray_colors(): void
    {
        $this->assertGreaterThanOrEqual(4, count(Utilities::getRayColors()));
        $this->assertContains('green', Utilities::getRayColors());
    }

    /** @test */
    public function it_sets_a_background_color(): void
    {
        $data = 'test';

        $this->assertStringContainsString('>test</div>', Utilities::addBackgroundColorToPayload($data, 'red'));
        $this->assertStringContainsString('bg-red', Utilities::addBackgroundColorToPayload($data, 'red'));
    }
}

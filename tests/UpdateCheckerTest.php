<?php

namespace Permafrost\RayCli\Tests;

use Permafrost\RayCli\UpdateChecker;
use PHPUnit\Framework\TestCase;

class UpdateCheckerTest extends TestCase
{
    /** @test */
    public function it_determines_when_an_update_is_available(): void
    {
        $checker = new UpdateChecker();

        $this->assertTrue($checker->isUpdateAvailable('2.0.0', '1.8.0'));
        $this->assertFalse($checker->isUpdateAvailable('1.0.0', '1.8.0'));
    }

    /** @test */
    public function it_returns_false_when_no_version_info_is_available(): void
    {
        $checker = new UpdateChecker();
        $checker->releaseApiUrl = 'http://localhost:18000/releases.json';

        $this->assertFalse($checker->isUpdateAvailable('2.0.0', ''));
        $this->assertFalse($checker->isUpdateAvailable('', '1.8.0'));
        $this->assertFalse($checker->isUpdateAvailable('2.0.0', null));
    }

    /** @test */
    public function it_retrieves_the_latest_releases_data(): void
    {
        $checker = new UpdateChecker();
        $data = $checker->retrieveLatestReleaseData();

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
    }

    /** @test */
    public function it_decodes_releases_json_data(): void
    {
        $checker = new UpdateChecker();

        $json = file_get_contents(__DIR__ . '/releases.json');
        $this->assertEquals('1.8.1', $checker->decodeReleasesData($json));
        $this->assertNull($checker->decodeReleasesData(substr($json, 0, 25)));

        $json = file_get_contents(__DIR__ . '/testfile.json');
        $this->assertNull($checker->decodeReleasesData($json));
    }

    /** @test */
    public function it_creates_an_instance_when_calling_create(): void
    {
        $this->assertNotNull(UpdateChecker::create());
    }
}

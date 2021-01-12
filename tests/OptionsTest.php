<?php

namespace Permafrost\RayCli\Tests;

use Permafrost\RayCli\Options;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{

    /** @test */
    public function it_resets_sizes(): void
    {
        $options = new Options();

        $options->large = true;
        $options->small = true;

        $options->resetSizes();

        $this->assertFalse($options->large);
        $this->assertFalse($options->small);
    }

    /** @test */
    public function it_detects_json_strings(): void
    {
        $this->assertTrue(Options::isJsonString('{"test": 123}'));
        $this->assertFalse(Options::isJsonString(''));
        $this->assertFalse(Options::isJsonString('hello world'));
    }

    /** @test */
    public function it_formats_a_string_for_html_payload(): void
    {
        $this->assertEquals('A&nbsp;B&nbsp;C', Options::formatStringForHtmlPayload('A B C'));
        $this->assertEquals("A<br />\nB<br />\nC", Options::formatStringForHtmlPayload("A\nB\nC"));
    }
}

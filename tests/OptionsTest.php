<?php

namespace Permafrost\RayCli\Tests;

use Permafrost\RayCli\Options;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

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

    /** @test */
    public function it_processes_the_clear_screen_option(): void
    {
        $input1 = new ArgvInput([]);
        $input2 = new ArgvInput(['--clear' => true]);
        $input3 = new ArgvInput(['--clear' => false]);

        $options = new Options();

        $options->clear = true;
        $options->processClearScreenOption($input1);
        $this->assertFalse($options->clear);

        $options->clear = true;
        $options->processClearScreenOption($input2);
        $this->assertFalse($options->clear);

        $options->clear = true;
        $options->processClearScreenOption($input3);
        $this->assertFalse($options->clear);
    }

    /** @test */
    public function it_gets_options(): void
    {
        $definition1 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
            new InputOption('screen', 's', InputOption::VALUE_OPTIONAL),
        ]);

        $definition2 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
            new InputOption('clear', 'C', InputOption::VALUE_NONE),
            new InputOption('screen', 's', InputOption::VALUE_OPTIONAL),
            new InputOption('color', 'c', InputOption::VALUE_REQUIRED),
            new InputOption('large', null, InputOption::VALUE_NONE),
        ]);

        $input1 = new ArgvInput(['bin/ray', '"test string"', '--screen'], $definition1);
        $input2 = new ArgvInput(['bin/ray', '--clear', '-s', 'test1', '--color=red', '"test string"'], $definition2);

        $this->assertEquals('default_value', Options::getOption($input1, 'clear', 'default_value'));
        $this->assertEquals(null, Options::getOption($input1, 'screen', 'default_value'));
        $this->assertEquals(true, Options::getOption($input2, 'clear', 'test'));
        $this->assertEquals('test1', Options::getOption($input2, 'screen', 'default_value'));
        $this->assertEquals('red', Options::getOption($input2, 'color', 'default_value'));
        $this->assertEquals(false, Options::getOption($input2, 'large', 'default_value'));
    }

    /** @test */
    public function it_gets_data_from_stdin(): void
    {
        $definition1 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
        ]);

        $definition2 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
            new InputOption('stdin', null, InputOption::VALUE_NONE),
        ]);

        $input1 = new ArgvInput(['bin/ray', 'test string'], $definition1);
        $input2 = new ArgvInput(['bin/ray', '--stdin'], $definition2);

        $options = new Options();
        $options->stdin = false;

        $this->assertEquals('test string', $options->getData($input1));

        $options->stdin = true;
        $options->stdinFile = __DIR__ . '/tempstdinfile.tmp';

        file_put_contents($options->stdinFile, '__TEST_STRING__');

        $this->assertEquals('__TEST_STRING__', $options->getData($input2));

        unlink($options->stdinFile);
    }

    /** @test */
    public function it_processes_the_screen_option(): void
    {
        $definition1 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
            new InputOption('screen', 's', InputOption::VALUE_OPTIONAL),
        ]);

        $definition2 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
            new InputOption('screen', 's', InputOption::VALUE_OPTIONAL),
        ]);

        $input1 = new ArgvInput(['bin/ray', '"test string"'], $definition1);
        $input2 = new ArgvInput(['bin/ray', '-s', 'test1', '"my string"'], $definition2);

        $options = new Options();
        //$options->screen = '';

        $options->processScreenOption($input1);
        $this->assertEquals(null, $options->screen);
    }

    /** @test */
    public function it_loads_the_color_options(): void
    {
        $definition1 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
            new InputOption('green', null, InputOption::VALUE_NONE),
            new InputOption('blue', null, InputOption::VALUE_NONE),
        ]);

        $input1 = new ArgvInput(['bin/ray', '"test string"', '--blue'], $definition1);
        $options = new Options();

        $this->assertFalse($options->blue);

        $options->loadColorOptions($input1);

        $this->assertTrue($options->blue);
        $this->assertFalse($options->green);
    }

    /** @test */
    public function it_loads_the_background_color_options(): void
    {
        $definition1 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
            new InputOption('bg-green', null, InputOption::VALUE_NONE),
            new InputOption('bg-blue', null, InputOption::VALUE_NONE),
        ]);

        $input1 = new ArgvInput(['bin/ray', '"test string"', '--bg-blue'], $definition1);
        $options = new Options();

        $this->assertFalse($options->bg_blue);

        $options->loadColorOptions($input1);

        $this->assertTrue($options->bg_blue);
        $this->assertFalse($options->bg_green);
    }

    /** @test */
    public function it_loads_files_as_the_payload(): void
    {
        $definition1 = new InputDefinition([
            new InputArgument('data', InputArgument::OPTIONAL),
            new InputOption('raw', null, InputOption::VALUE_NONE),
        ]);

        $input1 = new ArgvInput(['bin/ray', __DIR__ . '/testfile.json', '--raw'], $definition1);
        $options = new Options();
        $options->raw = true;
        $options->data = __DIR__ . '/testfile.json';
        $options->filename = __DIR__ . '/testfile.json';

        $options->loadFileContentAsData();

        //$options->data = json_encode($options->data, JSON_PRETTY_PRINT);

        $options->data = str_replace('&nbsp;', ' ', $options->data);
        $options->data = html_entity_decode($options->data, ENT_COMPAT, 'UTF-8');
        $options->data = str_replace('<br />', '', $options->data);


        $this->assertStringEqualsFile(__DIR__ . '/testfile.json', $options->data);
        $this->assertIsNotArray($options->jsonData);
        $this->assertNull($options->jsonData);
    }
}

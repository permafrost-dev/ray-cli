<?php

namespace Permafrost\RayCli\Tests;

use Permafrost\RayCli\RayCliCommand;
use Permafrost\RayCli\Utilities;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class RayCliCommandTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ray()->newScreen('ray-cli:test:' . __CLASS__ . '@' . date('H:i:s'));

        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        ray()->clearScreen();

        parent::tearDownAfterClass();
    }

    public function getCommand()
    {
        $command = new RayCliCommand();
        $command->setName('send');

        return $command;
    }

    public function getCommandTester()
    {
        $command = $this->getCommand();
        $app = $this->getApp($command);

        return new CommandTester($command);
    }

    public function getApp($command)
    {
        $app = (new Application())
            ->add($command);

        return Utilities::initializeCommand($app);
    }

    /** @test */
    public function it_sends_a_basic_string(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'my string']);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_sends_a_basic_string_with_a_background_color(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'my string', '--bg-red']);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_sends_a_basic_string_with_label_and_color(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => '"my string"', '-L' => 'my label', '-c' => 'red']);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_sends_a_delimited_list(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'one,two,three', '--csv' => true]);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'one;two;three', '-D' => ';']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_sends_a_notification(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'my string', '--notify' => true]);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** test */
    public function it_reads_data_from_stdin(): void
    {
        // TODO: fix this test, it hangs due to reading from stdin

        $tester = $this->getCommandTester();

        $tester->setInputs(['hello from standard input']);
        $tester->execute(['command' => 'send', '--stdin' => true], ['interactive' => false]);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_sends_the_contents_of_a_non_json_file(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => __FILE__]);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_sends_the_contents_of_a_json_file(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => __DIR__ . DIRECTORY_SEPARATOR . 'testfile.json']);

        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_creates_a_new_screen(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'test string', '--screen' => 'a new screen']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester->execute(['command' => 'send', 'data' => 'test string', '--screen' => true]);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester->execute(['command' => 'send', 'data' => 'test string', '-s' => null]);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester->execute(['command' => 'send', 'data' => 'test string', '-s' => '-']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester->execute(['command' => 'send', 'data' => 'test string', '--screen' => ' ']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester->execute(['command' => 'send', '--screen' => 'test']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester->execute(['command' => 'send', '--screen' => '']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_clears_the_screen(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'test string', '--clear' => true]);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_sends_sizes_with_named_flags(): void
    {
        $tester = $this->getCommandTester();

        $flags = ['large', 'small', 'sm', 'lg'];

        foreach ($flags as $flag) {
            $tester->execute(['command' => 'send', 'data' => 'test string', "--$flag" => true]);
            $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
        }
    }

    /** @test */
    public function it_sends_sizes_using_size_flags(): void
    {
        $tester = $this->getCommandTester();

        $sizes = ['large', 'lg', 'small', 'sm', 'normal', ''];

        foreach ($sizes as $size) {
            $tester->execute(['command' => 'send', 'data' => 'test string', '--size' => $size]);
            $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
        }
    }

    /** @test */
    public function it_sends_only_clear_screen(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', '--clear' => true]);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_shows_usage_message_when_no_flags_and_no_data_are_provided(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send']);
        $this->assertStringContainsString('Usage: ', $tester->getDisplay());
    }

    /** @test */
    public function it_sends_named_color_payload(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', '--red' => true, 'data' => 'test data']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_retrieves_the_requested_url(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'https://static.permafrost.dev/test-data.json']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_refreshes_the_display_of_a_file(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => __DIR__ . '/testfile.json', '--refresh' => '0.025']);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }

    /** @test */
    public function it_executes_the_parameter_and_displays_the_output(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => __DIR__ . '/scripts/testscript.php', '--exec' => true]);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester->execute(['command' => 'send', 'data' => __DIR__ . '/scripts/testscript.sh', '--exec' => true]);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());

        $tester->execute(['command' => 'send', 'data' => __DIR__ . '/scripts/testscript-noexec.sh', '--exec' => true]);
        $this->assertEquals(Command::SUCCESS, $tester->getStatusCode());
    }
}

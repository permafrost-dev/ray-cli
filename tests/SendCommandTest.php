<?php

namespace Permafrost\RayCli\Tests;

use Permafrost\RayCli\SendCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class SendCommandTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ray()->newScreen('ray-cli:test:' . __CLASS__);

        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        ray()->clearScreen();

        parent::tearDownAfterClass();
    }

    public function getCommand()
    {
        $command = new SendCommand();
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
        return (new Application())
            ->add($command)
            ->addArgument('data', InputArgument::OPTIONAL, 'The data to send to Ray.')
            ->addOption('color', 'c', InputOption::VALUE_REQUIRED, 'The payload color', 'default')
            ->addOption('csv', null, InputOption::VALUE_NONE, 'Sends the data as a comma-separated list')
            ->addOption('delimiter', 'D', InputOption::VALUE_REQUIRED, 'Sends the data as a list using the specified delimiter')
            ->addOption('json', 'j', InputOption::VALUE_NONE, 'Sends a json payload')
            ->addOption('label', 'L', InputOption::VALUE_REQUIRED, 'Sends a label with the payload')
            ->addOption('notify', 'N', InputOption::VALUE_NONE, 'Sends a notification payload')
            ->addOption('stdin', null, InputOption::VALUE_NONE, 'Read data from stdin')
            ;
//            ->setCode(function (ArrayInput $input, OutputInterface $output) {
//                print_r($input->getArguments());
//                return false;
//            });
    }

    /** @test */
    public function it_sends_a_basic_string(): void
    {
        $tester = $this->getCommandTester();

        $tester->execute(['command' => 'send', 'data' => 'my string']);

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
}

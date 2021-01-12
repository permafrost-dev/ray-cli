<?php

namespace Permafrost\RayCli;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * This is used to ensure parity between the bin/ray application options and the test application command
 * that's created during unit tests.
 *
 * The `$command` argument should be an instance of either `Symfony\Component\Console\SingleCommandApplication` or
 * the result of `(new Application())->add($cmd)` where `$cmd` is an instance of `Symfony\Console\Command` and
 * `Application` is an instance of `Symfony\Component\Console\Application`.
 *
 * @param $command
 *
 * @return mixed
 */
function initialize_command($command)
{
    return $command
        ->addArgument('data', InputArgument::OPTIONAL, 'The data to send to Ray.')
        ->addOption('clear', 'C', InputOption::VALUE_NONE, 'Clears the Ray screen')
        ->addOption('color', 'c', InputOption::VALUE_REQUIRED, 'The payload color', 'default')
        ->addOption('csv', null, InputOption::VALUE_NONE, 'Sends the data as a comma-separated list')
        ->addOption('delimiter', 'D', InputOption::VALUE_REQUIRED, 'Sends the data as a list using the specified delimiter')
        ->addOption('json', 'j', InputOption::VALUE_NONE, 'Sends a json payload')
        ->addOption('label', 'L', InputOption::VALUE_REQUIRED, 'Sends a label with the payload')
        ->addOption('large', null, InputOption::VALUE_NONE, 'Send the payload as large text')
        ->addOption('notify', 'N', InputOption::VALUE_NONE, 'Sends a notification payload')
        ->addOption('screen', 's', InputOption::VALUE_OPTIONAL, 'Create a new screen with an optional name')
        ->addOption('small', null, InputOption::VALUE_NONE, 'Send the payload as small text')
        ->addOption('stdin', null, InputOption::VALUE_NONE, 'Read data from stdin');
}

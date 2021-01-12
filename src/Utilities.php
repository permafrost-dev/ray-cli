<?php

namespace Permafrost\RayCli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\SingleCommandApplication;

class Utilities
{
    public const PACKAGE_NAME = 'permafrost-dev/ray-cli';

    static $app = null;

    /**
     * Returns the filename that was executed.
     *
     * @return string|null
     */
    public static function getExecutedFilename(): ?string
    {
        $argv = self::getArgv();
        $result = $_SERVER['SCRIPT_FILENAME'] ?? $argv[0] ?? null;

        return !$result ? $result : realpath($result);
    }

    /**
     * Determine if this script is running as a PHAR.
     *
     * @return bool
     */
    public static function runningAsPhar(): bool
    {
        $executedFile = self::getExecutedFilename();

        if (empty($executedFile) || !file_exists($executedFile)) {
            return false;
        }

        $filesize = filesize($executedFile);

        if ($filesize === false) {
            $filesize = 0;
        }

        // if the executing file ends with '.phar', we can assume it's a phar file.
        if ($filesize > 2048 && preg_match('~\.phar$~i', $executedFile) === 1) {
            return true;
        }

        // phar files are much larger than PHP scripts (usually upwards of 1 MB), whereas this file is
        // less than 2 KB, so using 12 KB as a threshold should usually return an accurate result.
        return $filesize > 12288;
    }

    public static function isRunningUnitTests(): bool
    {
        // phpunit is running or we are running under CI (i.e. github actions)
        return strtolower(getenv('APP_ENV')) === 'testing' || getenv('CI');
    }

    /**
     * This returns the package version, using a magic placeholder for the git tag version
     * when building the phar binary.
     *
     * if not running as a phar, the installed package version is used.
     *
     * @return string
     */
    public static function getPackageVersion(): string
    {
        if (self::isRunningUnitTests()) {
            return 'dev-main';
        }

        $version = '@git-version@';

        if (strpos($version, '@git') === 0 || !self::runningAsPhar()) {
            $version = \Composer\InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);
        }

        if (empty($version)) {
            $version = '1.x';
        }

        // ensure the version string is prefixed with 'v'
        if ($version[0] ?? '' !== 'v') {
            $version = "v$version";
        }

        return empty($version) ? 'v1.x' : $version;
    }

    public static function getArgv(): array
    {
        global $argv;

        return $_SERVER['argv'] ?? $argv;
    }

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
    public static function initializeCommand(Command $command): Command
    {
        self::$app = $command
            ->setHelp('help message')
            ->setDescription('Interact with and send data to Ray from the command line (https://myray.app)')
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

        return self::$app;
    }
}

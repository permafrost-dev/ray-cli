<?php

namespace Permafrost\RayCli;

use Symfony\Component\Console\Input\InputInterface;

class Options
{
    public bool $clear = false;
    public ?string $color = null;
    public bool $csv = false;
    public ?string $delimiter = null;
    public bool $json = false;
    public string $label = '';
    public bool $large = false;
    public bool $notify = false;
    public ?string $screen = null;
    public bool $small = false;
    public bool $stdin = false;

    // colors
    public bool $blue = false;
    public bool $gray = false;
    public bool $green = false;
    public bool $orange = false;
    public bool $purple = false;
    public bool $red = false;

    public ?string $data = '';
    public ?string $filename = null;

    /** @var string|string[]|array|mixed|null */
    public $jsonData = null;

    protected bool $resetDataToNull = false;

    public static function fromInput(InputInterface $input): self
    {
        $result = new self();

        self::loadOptionsFromInput($input, $result);

        $result->data = $result->getData($input);
        $result->jsonData = $result->getJsonData($input);

        if (!$result->delimiter && $result->csv) {
            $result->delimiter = ',';
        }

        $result->processScreenOption($input);
        $result->processClearScreenOption($input);

        if (!$result->data && !$result->resetDataToNull) {
            $result->data = '';
        }

        if (!empty($result->data) && file_exists($result->data) && is_file($result->data)) {
            $result->filename = realpath($result->data);
            $content = file_get_contents($result->filename);

            $result->data = self::formatStringForHtmlPayload($content);

            if (self::isJsonString($result->data)) {
                $result->jsonData = json_decode($result->data, true);
            } elseif (empty($result->label)) {
                // if no label exists, use the filename
                // this only applies to non-json files
                $result->label = $result->filename ?? '(unknown filename)';
            }
        }

        return $result;
    }

    /**
     * Loads options from `$input` into the instance properties.
     *
     * @param InputInterface $input
     * @param Options $result
     */
    protected static function loadOptionsFromInput(InputInterface $input, Options $result): void
    {
        // string options
        $result->color = self::getOption($input, 'color', null);
        $result->delimiter = self::getOption($input, 'delimiter', null);
        $result->label = (string)self::getOption($input, 'label', '');
        $result->screen = self::getOption($input, 'screen', null);

        // boolean options
        $result->clear = (bool)self::getOption($input, 'clear', false);
        $result->csv = (bool)self::getOption($input, 'csv', false);
        $result->json = (bool)self::getOption($input, 'json', false);
        $result->large = (bool)self::getOption($input, 'large', false);
        $result->notify = (bool)self::getOption($input, 'notify', false);
        $result->small = (bool)self::getOption($input, 'small', false);
        $result->stdin = (bool)self::getOption($input, 'stdin', false);

        // color options
        foreach (Utilities::getRayColors() as $color) {
            $result->{$color} = (bool)self::getOption($input, $color, false);

            // use the first flag found, in case multiple color flags are passed
            if ($result->{$color}) {
                break;
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param string $name
     * @param mixed|null $default
     *
     * @return bool|mixed|string|string[]|null
     */
    protected static function getOption(InputInterface $input, string $name, $default)
    {
        if ($input->hasOption($name) && !$input->getOption($name)) {
            return $default;
        }

        return $input->getOption($name);
    }

    protected function getData(InputInterface $input): ?string
    {
        if ($this->stdin) {
            return file_get_contents('php://stdin');
        }

        return $input->getArgument('data');
    }

    /**
     * @param InputInterface $input
     *
     * @return mixed|null
     */
    protected function getJsonData(InputInterface $input)
    {
        $isJson = self::getOption($input, 'json', false);
        $result = null;

        if (is_string($this->data) || $isJson) {
            try {
                $result = json_decode($this->data, true, 512, JSON_THROW_ON_ERROR);
                $this->json = true;
            } catch (\JsonException $e) {
                return null;
            }
        }

        return $result;
    }

    public static function isJsonString($text): bool
    {
        if (!is_string($text) || empty($text)) {
            return false;
        }

        try {
            json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return false;
        }

        return true;
    }

    public static function formatStringForHtmlPayload(string $text): string
    {
        $encodedText = str_replace(' ', '&nbsp;', htmlentities($text));

        return nl2br($encodedText);
    }

    /**
     * @param InputInterface $input
     *
     * @return bool
     */
    protected function processScreenOption(InputInterface $input): void
    {
        if (!$input->hasOption('screen')) {
            $this->screen = null;

            return;
        }

        if ($input->hasOption('screen') && $input->getOption('screen') === null) {
            $this->screen = null;

            return;
        }

        if (!$this->data) {
            $this->resetDataToNull = true;

            if (!$this->screen) {
                $this->screen = '-';
            }
        }

        if ($this->screen === '-') {
            $this->screen = ' ';
        }

        if ($this->screen && $this->screen === ' ') {
            $this->screen = ' ';
            $this->clear = false;
        }
    }

    protected function processClearScreenOption(InputInterface $input): void
    {
        if (!$input->hasOption('clear')) {
            $this->clear = false;
        }

        if ($input->hasOption('clear') && $input->getOption('clear') === null) {
            $this->clear = false;
        }
    }

    public function resetSizes(): void
    {
        $this->large = false;
        $this->small = false;
    }
}

<?php

namespace Permafrost\RayCli;

use Symfony\Component\Console\Input\InputInterface;

class Options
{
    public string $stdinFile = 'php://stdin';
    public ?string $backgroundColor = null;

    public bool $clear = false;
    public ?string $color = null;
    public bool $csv = false;
    public ?string $delimiter = null;
    public bool $json = false;
    public string $label = '';
    public bool $large = false;
    public bool $notify = false;
    public bool $raw = false;
    public ?string $screen = null;
    public ?string $size = null;
    public bool $small = false;
    public bool $stdin = false;

    // colors
    public bool $blue = false;
    public bool $gray = false;
    public bool $green = false;
    public bool $orange = false;
    public bool $purple = false;
    public bool $red = false;

    // colors
    public bool $bg_blue = false;
    public bool $bg_gray = false;
    public bool $bg_green = false;
    public bool $bg_orange = false;
    public bool $bg_purple = false;
    public bool $bg_red = false;

    public ?string $data = '';
    public ?string $filename = null;
    public ?string $url = null;

    /** @var string|string[]|array|mixed|null */
    public $jsonData = null;

    protected bool $resetDataToNull = false;

    public static function fromInput(InputInterface $input): self
    {
        $result = new self();

        $result->loadOptionsFromInput($input);

        $result->data = $result->getData($input);
        $result->jsonData = $result->getJsonData($input);
        $result->url = $result->getUrl($input);

        if (!$result->delimiter && $result->csv) {
            $result->delimiter = ',';
        }

        $result->processScreenOption($input);
        $result->processClearScreenOption($input);

        if (!$result->data && !$result->resetDataToNull) {
            $result->data = '';
        }

        $isValidFile = !empty($result->data)
            && file_exists($result->data)
            && is_file($result->data);

        if ($isValidFile) {
            $result->loadFileContentAsData();
        }

        return $result;
    }

    /**
     * Loads options from `$input` into the instance properties.
     *
     * @param InputInterface $input
     *
     * @return Options
     */
    public function loadOptionsFromInput(InputInterface $input): self
    {
        // string options
        $this->color = self::getOption($input, 'color', null);
        $this->delimiter = self::getOption($input, 'delimiter', null);
        $this->label = (string)self::getOption($input, 'label', '');
        $this->screen = self::getOption($input, 'screen', null);
        $this->size = self::getOption($input, 'size', null);

        // boolean options
        $this->clear = (bool)self::getOption($input, 'clear', false);
        $this->csv = (bool)self::getOption($input, 'csv', false);
        $this->json = (bool)self::getOption($input, 'json', false);
        $this->large = (bool)self::getOption($input, 'large', false);
        $this->notify = (bool)self::getOption($input, 'notify', false);
        $this->raw = (bool)self::getOption($input, 'raw', false);
        $this->small = (bool)self::getOption($input, 'small', false);
        $this->stdin = (bool)self::getOption($input, 'stdin', false);

        $this->loadSizeOptions($input);
        $this->loadColorOptions($input);

        return $this;
    }

    /**
     * @param InputInterface $input
     * @param string $name
     * @param mixed|null $default
     *
     * @return bool|mixed|string|string[]|null
     */
    public static function getOption(InputInterface $input, string $name, $default)
    {
        if ($input->hasOption($name)) {
            return $input->getOption($name);
        }

        if ($input->hasOption($name) && !$input->getOption($name)) {
            return $default;
        }

        if (!$input->hasOption($name)) {
            return $default;
        }

        return $input->getOption($name);
    }

    /**
     * Gets the value of the "data" argument passed on the command line.
     * If the value of data === '-' or the --stdin flag was passed,
     * the data is read from stdin instead.
     *
     * @param InputInterface $input
     *
     * @return string|null
     */
    public function getData(InputInterface $input): ?string
    {
        $data = $input->getArgument('data');

        if ($this->stdin || $data === '-') {
            return file_get_contents($this->stdinFile);
        }

        return $data;
    }

    /**
     * Returns decoded JSON data.
     *
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

    protected function getUrl(InputInterface $input)
    {
        if (!is_string($this->data) || $this->json) {
            return null;
        }

        if (!parse_url($this->data) || strpos($this->data, 'http') !== 0) {
            return null;
        }

        return $this->data;
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
    public function processScreenOption(InputInterface $input): void
    {
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

    public function processClearScreenOption(InputInterface $input): void
    {
        if (!$input->hasOption('clear')) {
            $this->clear = false;
        }

        if ($input->hasOption('clear') && $input->getOption('clear') === null) {
            $this->clear = false;
        }
    }

    /**
     * Load size options into the class instance.
     *
     * @param InputInterface $input
     *
     * @return Options
     */
    protected function loadSizeOptions(InputInterface $input): self
    {
        // map --lg to --large and --sm to --small
        $aliases = ['large' => 'lg', 'small' => 'sm'];

        foreach ($aliases as $long => $short) {
            if (!$this->{$long}) {
                $this->{$long} = (bool)self::getOption($input, $short, false);
            }
            if (in_array($this->size, [$long, $short], true)) {
                $this->{$long} = true;
            }
        }

        if ($this->size === 'normal') {
            $this->large = false;
            $this->small = false;
        }

        return $this;
    }

    /**
     * @return Options
     */
    public function loadFileContentAsData(): self
    {
        $this->filename = realpath($this->data);
        $content = file_get_contents($this->filename);

        if (empty($this->label)) {
            $this->label = $this->filename ?? '(unknown filename)';
        }

        $this->data = $content;

        if ($this->raw) {
            $this->data = self::formatStringForHtmlPayload($content);

            return $this;
        }

        if (self::isJsonString($content)) {
            $this->jsonData = json_decode($content, true);
        }

        return $this;
    }

    public function resetSizes(): void
    {
        $this->large = false;
        $this->small = false;
    }

    /**
     * Loads color options into the class instance.
     *
     * @param InputInterface $input
     *
     * @return Options
     */
    public function loadColorOptions(InputInterface $input): self
    {
        foreach (Utilities::getRayColors() as $color) {
            $this->{$color} = (bool)self::getOption($input, $color, false);

            // use the first flag found, in case multiple color flags are passed
            if ($this->{$color}) {
                break;
            }
        }

        foreach (Utilities::getRayColors() as $color) {
            $bgColorVar = "bg_{$color}";
            $this->{$bgColorVar} = (bool)self::getOption($input, "bg-$color", false);

            // use the first flag found, in case multiple color flags are passed
            if ($this->{$bgColorVar}) {
                $this->backgroundColor = $color;

                break;
            }
        }

        return $this;
    }
}

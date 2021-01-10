<?php

namespace Permafrost\RayCli;

use Symfony\Component\Console\Input\InputInterface;

class Options
{
    public ?string $color = null;
    public bool $csv = false;
    public ?string $delimiter = null;
    public bool $json = false;
    public string $label = '';
    public bool $notify = false;
    public bool $stdin = false;

    public ?string $data = '';
    public ?string $filename = null;

    /** @var string|string[]|array|mixed|null */
    public $jsonData = null;

    public static function fromInput(InputInterface $input): self
    {
        $result = new self();

        $result->color = self::getOption($input, 'color', null);
        $result->delimiter = self::getOption($input, 'delimiter', null);
        $result->label = (string)self::getOption($input, 'label', '');

        $result->csv = (bool)self::getOption($input, 'csv', false);
        $result->json = (bool)self::getOption($input, 'json', false);
        $result->notify = (bool)self::getOption($input, 'notify', false);
        $result->stdin = (bool)self::getOption($input, 'stdin', false);

        $result->data = $result->getData($input);
        $result->jsonData = $result->getJsonData($input);

        if (!$result->data) {
            $result->data = '';
        }

        if (file_exists($result->data) && is_file($result->data)) {
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

        if (!$result->delimiter && $result->csv) {
            $result->delimiter = ',';
        }

        return $result;
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

        if (is_string($this->data) || $isJson) {
            try {
                return json_decode($this->data, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                return null;
            }
        }

        return null;
    }

    protected static function isJsonString($text): bool
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

    protected static function formatStringForHtmlPayload(string $text): string
    {
        $encodedText = str_replace(' ', '&nbsp;', htmlentities($text));

        return nl2br($encodedText);
    }
}

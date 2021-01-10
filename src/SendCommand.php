<?php

namespace Permafrost\RayCli;

use Spatie\Ray\ArgumentConverter;
use Spatie\Ray\Ray;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends Command
{
    protected Ray $payload;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->payload = ray();
        $options = Options::fromInput($input);

        $this->sendInitialPayload($options)
            ->sendColorPayload($options);

        $output->writeln('<info>Sent data to Ray.</info>');

        return Command::SUCCESS;
    }

    /**
     * Sends the input data to Ray and updates the instance `$payload` property.
     * Returns `$this` to allow chaining method calls.
     *
     * @param Options $options
     *
     * @return SendCommand
     */
    protected function sendInitialPayload(Options $options): self
    {
        // send a notification payload
        if ($options->notify) {
            $this->payload = $this->payload->notify($options->data);

            return $this;
        }

        // send a delimited list payload
        if ($options->delimiter) {
            $items = explode($options->delimiter, $options->data);
            $data = ArgumentConverter::convertToPrimitive($items);

            $this->payload = $this->payload->send($data);

            return $this;
        }

        // send a decoded json payload
        if ($options->jsonData) {
            $data = ArgumentConverter::convertToPrimitive($options->jsonData);

            $this->payload = $this->payload->send($data);

            return $this;
        }

        // send the string argument as the payload with optional label
        $this->payload = $this->payload->sendCustom($options->data, $options->label);

        return $this;
    }

    /**
     * Sends a color payload to Ray.
     *
     * @param Options $options
     *
     * @return $this
     */
    protected function sendColorPayload(Options $options): self
    {
        if ($options->color) {
            $this->payload->color($options->color);
        }

        return $this;
    }
}

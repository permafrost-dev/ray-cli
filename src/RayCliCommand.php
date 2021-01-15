<?php

namespace Permafrost\RayCli;

use Spatie\Ray\ArgumentConverter;
use Spatie\Ray\Ray;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RayCliCommand extends Command
{
    protected bool $updatedPayload = false;
    protected Ray $payload;
    protected Options $options;
    protected InputInterface $input;

    public function execute(InputInterface $input, ?OutputInterface $output)
    {
        $this->input = $input;

        $this->checkForUpdates($output);

        $this->initializeCommand($input);

        if (!$this->ensureDataExistsToSend($this->options, $output)) {
            return Command::FAILURE;
        }

        $this->sendInitialPayload($this->options)
            ->sendColorPayload($this->options)
            ->sendNamedColorPayload($this->options)
            ->sendSizePayload($this->options);

        $output->writeln('<info>Sent data to Ray.</info>');

        return Command::SUCCESS;
    }

    protected function initializeCommand(InputInterface $input): self
    {
        $this->options = Options::fromInput($input);
        $this->payload = ray();

        return $this;
    }

    protected function ensureDataExistsToSend(Options $options, OutputInterface $output): bool
    {
        if (!$options->data && !$options->stdin && !$options->clear && !$options->screen) {
            $usage = Utilities::$app->getSynopsis(false);

            $output->writeln("<info>Usage:</info> {$usage}");
            $output->writeln('');

            return false;
        }

        return true;
    }

    /**
     * Sends the input data to Ray and updates the instance `$payload` property.
     * Returns `$this` to allow chaining method calls.
     *
     * @param Options $options
     *
     * @return RayCliCommand
     */
    protected function sendInitialPayload(Options $options): self
    {
        // clear takes precedence over screen
        if ($this->sendOnlyClearScreen($options)) {
            return $this;
        }

        if ($this->sendOnlyCreateNewScreen($options)) {
            return $this;
        }

        $this->sendNotification($options)
            ->sendNewScreen($options)
            ->sendClearScreen($options) // takes precedence over screen, so call after sendNewScreen()
            ->retrieveRequestedUrl($options) // must be done before the sendXXX methods
            ->sendDelimitedList($options)
            ->sendDecodedJson($options)
            ->sendCustomData($options); // must be called last

        if ($options->refresh && is_numeric($options->refresh)) {
            $this->refresh((float)$options->refresh, 10);
        }

        return $this;
    }

    protected function refresh(float $delay, int $times): self
    {
        $counter = 1;

        while (true) {
            $options = Options::fromInput($this->input);

            $this->retrieveRequestedUrl($options) // must be done before the sendXXX methods
                ->sendDelimitedList($options)
                ->sendDecodedJson($options)
                ->sendCustomData($options, true);

            usleep($delay * 1000000);

            $counter++;

            if ($counter > $times) {
                break;
            }
        }

        return $this;
    }

    /** @codeCoverageIgnore */
    protected function checkForUpdates(OutputInterface $output): void
    {
        // 25% chance to check for updates, to avoid repeatedly hitting the github api
        if (random_int(0, 100) < 75) {
            return;
        }

        $checker = UpdateChecker::create();

        $latest = $checker->retrieveLatestRelease();

        if ($checker->isUpdateAvailable($latest, null)) {
            $output->writeln("<info>There is a new release available: $latest</info>");
            $output->writeln("<info>You can download it from https://github.com/permafrost-dev/ray-cli/releases/tag/$latest</info>\n");
        }
    }

    protected function updatePayload(Ray $payload, bool $markUpdated = true): void
    {
        $this->payload = $payload;
        $this->updatedPayload = $markUpdated;
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
            $this->updatePayload($this->payload->color($options->color), false);
        }

        return $this;
    }

    protected function sendSizePayload(Options $options): self
    {
        if ($options->large) {
            $this->payload->large();
        }

        if ($options->small) {
            $this->payload->small();
        }

        return $this;
    }

    public function sendNamedColorPayload(Options $options): self
    {
        foreach (Utilities::getRayColors() as $color) {
            if ($options->{$color}) {
                $this->updatePayload($this->payload->{$color}());
            }
        }

        return $this;
    }

    protected function sendOnlyClearScreen(Options $options): bool
    {
        if (empty($options->data) && $options->clear) {
            // only clear the screen and nothing else
            $this->payload->clearScreen();

            // since no data is being sent, ignore the color/size flags so we don't
            // send an empty payload with just a color or size.
            $options->color = null;
            $options->resetSizes();

            return true;
        }

        return false;
    }

    protected function sendOnlyCreateNewScreen(Options $options): bool
    {
        if (empty($options->data) && $options->screen) {
            // only create a new screen and nothing else
            $this->payload->newScreen($options->screen);

            // ignore the color flags since no data is being sent
            $options->color = null;

            return true;
        }

        return false;
    }

    protected function sendNotification(Options $options): self
    {
        // send a notification payload

        if ($options->notify) {
            $this->updatePayload($this->payload->notify($options->data));
        }

        return $this;
    }

    protected function sendNewScreen(Options $options): self
    {
        // request that a new screen is created with name (optional)

        if ($options->screen) {
            // don't call updatePayload(), otherwise a text payload will not be sent
            $this->payload = $this->payload->newScreen($options->screen);
        }

        return $this;
    }

    protected function sendClearScreen(Options $options): self
    {
        // request that the screen is cleared (which creates a new screen without a name)

        if ($options->clear) {
            // don't call updatePayload(), otherwise a text payload will not be sent
            $this->payload = $this->payload->clearScreen();
        }

        return $this;
    }

    protected function sendDelimitedList(Options $options): self
    {
        // send a delimited list payload

        if ($options->delimiter) {
            $items = explode($options->delimiter, $options->data);
            $data = ArgumentConverter::convertToPrimitive($items);

            $this->updatePayload($this->payload->send($data));
        }

        return $this;
    }

    protected function sendDecodedJson(Options $options): self
    {
        // send a decoded json payload

        if ($options->jsonData || $options->json) {
            $this->updatePayload($this->payload->json(json_encode($options->jsonData)));
        }

        return $this;
    }

    protected function retrieveRequestedUrl(Options $options): self
    {
        if ($options->url) {
            $client = new UrlClient();

            $data = $client->retrieve('get', $options->url);

            $options->label = $options->url ?? 'URL';
            $options->data = $data;
            $options->json = $options::isJsonString($data);
            $options->filename = null;
        }

        return $this;
    }

    protected function sendCustomData(Options $options, bool $force = false): self
    {
        // send the string argument as the payload with optional label

        if (!$this->updatedPayload || $force) {
            $data = $options->data;

            if ($options->execOutput) {
                $data = trim(implode(PHP_EOL, $options->execOutput));
            }

            $data = Utilities::addBackgroundColorToPayload($data, $options->backgroundColor);

            $this->updatePayload($this->payload->sendCustom($data, $options->label));
        }

        return $this;
    }
}

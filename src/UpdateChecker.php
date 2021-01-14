<?php

namespace Permafrost\RayCli;

class UpdateChecker
{
    public string $releaseApiUrl = 'https://api.github.com/repos/permafrost-dev/ray-cli/releases';

    public function retrieveLatestReleaseData(): ?string
    {
        $url = $this->releaseApiUrl;
        $client = new UrlClient();

        return $client->retrieve('get', $url);
    }

    /** @codeCoverageIgnore */
    public function retrieveLatestRelease(): ?string
    {
        $data = $this->retrieveLatestReleaseData();

        return $this->decodeReleasesData($data);
    }

    public function decodeReleasesData(string $json): ?string
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            return null;
        }

        if (is_array($data) && count($data) && isset($data[0]['tag_name'])) {
            return $data[0]['tag_name'] ?? null;
        }

        return null;
    }

    public function isUpdateAvailable(?string $latestVersion = null, ?string $currentVersion = null): bool
    {
        $latestVersion = $latestVersion ?? $this->retrieveLatestRelease();
        $currentVersion = $currentVersion ?? Utilities::getPackageVersion();

        if ($currentVersion === 'dev-main') {
            $currentVersion = '99.99.99';
        }

        if (empty($latestVersion) || empty($currentVersion)) {
            return false;
        }

        return version_compare($latestVersion, $currentVersion, '>');
    }

    public static function create(): self
    {
        return new static();
    }
}

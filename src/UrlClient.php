<?php

namespace Permafrost\RayCli;

class UrlClient
{
    public function retrieve(string $method, string $url): ?string
    {
        $result = null;

        try {
            $curlHandle = $this->getCurlHandleForUrl($method, $url);

            $curlError = null;

            $result = curl_exec($curlHandle);

            if (curl_errno($curlHandle)) {
                $curlError = curl_error($curlHandle);
            }

            if ($curlError) {
                $result = $curlError;
            }
        } finally {
            curl_close($curlHandle);
        }

        return $result;
    }

    protected function getCurlHandleForUrl(string $method, string $url)
    {
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, $url);

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array_merge([
            'Accept: text/html, application/json, */*',
        ]));

        curl_setopt($curlHandle, CURLOPT_USERAGENT, 'ray-cli 1.0');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 15);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlHandle, CURLOPT_ENCODING, '');
        curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);

        $method = strtolower($method);

        if ($method === 'delete') {
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        if ($method === 'head') {
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'HEAD');
        }

        if ($method === 'patch') {
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PATCH');
        }

        if ($method === 'post') {
            curl_setopt($curlHandle, CURLOPT_POST, true);
        }

        if ($method === 'put') {
            curl_setopt($curlHandle, CURLOPT_PUT, true);
        }

        return $curlHandle;
    }
}

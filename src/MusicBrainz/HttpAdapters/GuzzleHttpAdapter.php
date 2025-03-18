<?php

namespace MusicBrainz\HttpAdapters;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use MusicBrainz\Exception;

/**
 * GuzzleHttp Adapter
 */
class GuzzleHttpAdapter extends AbstractHttpAdapter
{
    public function __construct(
        private readonly ClientInterface $client, // The Guzzle client used to make cURL requests
        ?string $endpoint = null
    ) {
        if (
            $endpoint !== null &&
            filter_var($endpoint, FILTER_VALIDATE_URL)
        ) {
            $this->endpoint = $endpoint;
        }
    }

    /**
     * Perform an HTTP request on MusicBrainz
     *
     * @throws Exception|GuzzleException
     */
    public function call(
        string $path,
        array $params = [],
        array $options = [],
        bool $isAuthRequired = false,
        bool $returnArray = false
    ): array|object {
        if ($options['user-agent'] == '') {
            throw new Exception('You must set a valid User Agent before accessing the MusicBrainz API');
        }

        $guzzleOptions = [
            'base_uri' => $this->endpoint . '/',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => $options['user-agent'],
            ],
            'query' => $params,
        ];

        if ($isAuthRequired) {
            if (
                $options['user'] != null &&
                $options['password'] != null
            ) {
                $guzzleOptions['auth'] = [
                    $options['user'],
                    $options['password'],
                    'digest',
                ];
            } else {
                throw new Exception('Authentication is required');
            }
        }

        $request = $this->client->request('GET', $path, $guzzleOptions);

        // musicbrainz throttle
        sleep(1);

        return json_decode((string) $request->getBody(), $returnArray);
    }
}

<?php

namespace MusicBrainz\HttpAdapters;

use Guzzle\Http\ClientInterface;
use MusicBrainz\Exception;

/**
 * Guzzle Http Adapter
 */
class GuzzleHttpAdapter extends AbstractHttpAdapter
{
    /**
     * Initializes the class.
     */
    public function __construct(
        private readonly ClientInterface $client, // The Guzzle client used to make cURL requests
        ?string $endpoint = null
    ) {
        if (filter_var($endpoint, FILTER_VALIDATE_URL)) {
            $this->endpoint = $endpoint;
        }
    }

    /**
     * Perform an HTTP request on MusicBrainz
     *
     * @param  string  $path
     * @param  boolean $isAuthRequired
     * @param  boolean $returnArray disregarded
     * @throws Exception
     * @return array
     */
    public function call($path, array $params = [], array $options = [], $isAuthRequired = false, $returnArray = false)
    {
        if ($options['user-agent'] == '') {
            throw new Exception('You must set a valid User Agent before accessing the MusicBrainz API');
        }

        $this->client->setBaseUrl($this->endpoint);
        $this->client->setConfig(
            array_merge(
                $this->client->getConfig()->toArray(),
                [
                    'data' => $params
                ]
            )
        );

        $request = $this->client->get($path . '{?data*}');
        $request->setHeader('Accept', 'application/json');
        $request->setHeader('User-Agent', $options['user-agent']);

        if ($isAuthRequired) {
            if ($options['user'] != null && $options['password'] != null) {
                $request->setAuth($options['user'], $options['password'], CURLAUTH_DIGEST);
            } else {
                throw new Exception('Authentication is required');
            }
        }

        $request->getQuery()->useUrlEncoding(false);

        // musicbrainz throttle
        sleep(1);

        return $request->send()->json();
    }
}

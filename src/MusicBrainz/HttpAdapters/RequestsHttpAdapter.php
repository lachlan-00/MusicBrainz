<?php

namespace MusicBrainz\HttpAdapters;

use MusicBrainz\Exception;
use Requests;

/**
 * Requests HTTP Client Adapter
 */
class RequestsHttpAdapter extends AbstractHttpAdapter
{
    /**
     * Initializes the class.
     *
     * @param null $endpoint Override the default endpoint (useful for local development)
     */
    public function __construct($endpoint = null)
    {
        if (filter_var($endpoint, FILTER_VALIDATE_URL)) {
            $this->endpoint = $endpoint;
        }
    }

    /**
     * Perform an HTTP request on MusicBrainz
     *
     * @param  string  $path
     * @param  boolean $isAuthRequired
     * @param  boolean $returnArray force json_decode to return an array instead of an object
     * @throws Exception
     * @return array
     */
    public function call($path, array $params = [], array $options = [], $isAuthRequired = false, $returnArray = false)
    {
        if ($options['user-agent'] == '') {
            throw new Exception('You must set a valid User Agent before accessing the MusicBrainz API');
        }

        $url = $this->endpoint . '/' . $path;
        $i   = 0;
        foreach ($params as $name => $value) {
            $url .= ($i++ == 0) ? '?' : '&';
            // AbstractFilter already urlencodes the Lucene escaped Query parts, so don't do it twice
            $url .= $name . '=' . $value;
        }

        $headers = [
            'Accept' => 'application/json',
            'User-Agent' => $options['user-agent']
        ];

        $requestOptions = [];
        if ($isAuthRequired) {
            if ($options['user'] != null && $options['password'] != null) {
                $requestOptions['auth'] = [$options['user'], $options['password']];
            } else {
                throw new Exception('Authentication is required');
            }
        }

        $request = Requests::get($url, $headers, $requestOptions);

        // musicbrainz throttle
        sleep(1);

        return json_decode($request->body, $returnArray);
    }
}

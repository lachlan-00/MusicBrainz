<?php

namespace MusicBrainz\HttpAdapters;

use MusicBrainz\Exception;
use WpOrg\Requests\Requests;

/**
 * Requests HTTP Client Adapter
 */
class RequestsHttpAdapter extends AbstractHttpAdapter
{
    public function __construct(?string $endpoint = null)
    {
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
     * @param string $path
     * @param array $params
     * @param array $options
     * @param boolean $isAuthRequired
     * @param boolean $returnArray force json_decode to return an array instead of an object
     * @return array|object
     * @throws Exception
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

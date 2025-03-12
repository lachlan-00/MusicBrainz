<?php

namespace MusicBrainz\HttpAdapters;

/**
 * MusicBrainz HTTP Client interface
 */
abstract class AbstractHttpAdapter
{
    /**
     * @var string
     */
    public $endpoint = 'https://musicbrainz.org/ws/2';

    /**
     * Perform an HTTP request on MusicBrainz
     *
     * @param string $path
     * @param boolean $isAuthRequired
     * @param boolean $returnArray
     *
     * @return array
     */
    abstract public function call($path, array $params = [], array $options = [], $isAuthRequired = false, $returnArray = false);
}

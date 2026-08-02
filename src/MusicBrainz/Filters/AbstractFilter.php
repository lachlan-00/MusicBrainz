<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

/**
 * This is the abstract filter which
 * contains the constructor which all
 * filters share because the only
 * difference between each filter class
 * is the valid argument types.
 *
 */
abstract class AbstractFilter
{
    /** @var string[] $validArgTypes */
    protected array $validArgTypes;

    protected array $validArgs = []; // The valid arguments/query parameters used when querying MusicBrainz

    /** @var string[] */
    protected array $protectedArgs = [
        'arid',
        'reid',
        'rgid',
        'tid',
    ];

    /**
     * __construct
     *
     * @param string[]|null $args
     */
    public function __construct(?array $args = null)
    {
        self::validateArgs($args);
    }

    /**
     * validateArgs
     * Set query parameters from the list of valid includes
     *
     * @param string[]|null $args
     */
    public function validateArgs(?array $args = null): void
    {
        if (is_array($args)) {
            $this->validArgs = [];
            foreach ($args as $key => $value) {
                if (in_array($key, $this->validArgTypes)) {
                    $this->validArgs[$key] = $value;
                }
            }
        }
    }

    /**
     * createParameters
     * @param array<string, string|int|null> $params
     * @return array<string, string|int|null>
     */
    public function createParameters(array $params = []): array
    {
        // The query is always rebuilt from the filter's valid args, so a caller-supplied query is discarded
        $params = ['query' => ''] + $params;

        if ($this->validArgs === []) {
            return $params;
        }

        foreach ($this->validArgs as $key => $val) {
            if ($params['query'] != '') {
                $params['query'] .= '+AND+';
            }

            if (!in_array($key, $this->protectedArgs)) {
                // Lucene escape characters
                $val = urlencode(
                    (string)preg_replace('/([(){}\[\]^"~:\\/])/', '\\\$1', (string)$val)
                );
            }

            // If the search string contains a space, wrap it in quotes
            // This isn't always wanted, but for the searches required in this library.
            if (preg_match('/[+]/', (string)$val)) {
                $val = '"' . $val . '"';
            }

            $params['query'] .= $key . ':' . $val;
        }

        return $params;
    }
}

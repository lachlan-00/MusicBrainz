<?php

namespace MusicBrainz\Filters;

use MusicBrainz\Label;
use MusicBrainz\MusicBrainz;

/**
 * This is the label filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class LabelFilter extends AbstractFilter implements FilterInterface
{
    protected array $validArgTypes = [
        'alias', // fix typo
        'begin',
        'code',
        'comment',
        'country',
        'end',
        'ended',
        'ipi',
        'label',
        'labelaccent',
        'laid',
        'sortname',
        'tag',
        'type'
    ];

    /**
     * @return string
     */
    public function getEntity()
    {
        return 'label';
    }

    /**
     * @return Label[]
     */
    public function parseResponse(array $response, MusicBrainz $brainz)
    {
        $labels = [];

        foreach ($response['labels'] as $label) {
            $labels[] = new Label($label, $brainz);
        }

        return $labels;
    }
}

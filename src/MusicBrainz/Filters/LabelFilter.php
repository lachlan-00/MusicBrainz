<?php

declare(strict_types=1);

namespace MusicBrainz\Filters;

use MusicBrainz\Entities\Label;
use MusicBrainz\MusicBrainz;

/**
 * This is the label filter and it contains
 * an array of valid argument types to be used
 * when querying the MusicBrainz web service.
 */
class LabelFilter extends AbstractFilter implements FilterInterface
{
    private const ENTITY = 'label';

    private const LINKS = [
        'area',
        'collection',
        'release'
    ];

    /** @var string[] $INCLUDES */
    public const INCLUDES = [
        'aliases',
        'genres',
        'ratings',
        'tags',
        'user-ratings',
        'user-tags',
    ];

    /** @var string[] $validArgTypes */
    protected array $validArgTypes = [
        'alias',
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
        'type',
    ];

    public function getEntity(): string
    {
        return self::ENTITY;
    }

    public function hasLink(string $entity): bool
    {
        return in_array($entity, self::LINKS);
    }

    /** @return string[] */
    public function getIncludes(): array
    {
        return self::INCLUDES;
    }

    /**
     * @return Label[]
     */
    public function parseResponse(
        array $response,
        MusicBrainz $brainz
    ): array {
        $labels = [];

        foreach ($response['labels'] as $label) {
            $labels[] = new Label($label, $brainz);
        }

        return $labels;
    }
}

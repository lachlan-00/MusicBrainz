<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\Tag;

/**
 * Represents a MusicBrainz url object
 * @package MusicBrainz
 */
class Url extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $resource;

    /** @var Tag[] $tags */
    public ?array $tags = null;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $url
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $url,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($url['id']) ||
            !$this->hasValidId($url['id'])
        ) {
            throw new Exception('Can not create url object. Missing valid MBID');
        }

        $this->brainz   = $brainz;
        $this->data     = $url;
        $this->id       = (string)$url['id'];
        $this->resource = (string)$url['resource'];
        $this->tags     = $url['tags'] ?? null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->resource;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array{
     *     id: string,
     *     resource: string,
     *     tags: Tag[]|null
     * }
     */
    public function getProps(): array
    {
        return [
            'id' => $this->id,
            'resource' => $this->resource,
            'tags' => $this->tags,
        ];
    }
}

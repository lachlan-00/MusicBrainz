<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz genre object
 * @package MusicBrainz
 */
class Genre extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $name;

    public string $disambiguation;

    private array $data;

    protected MusicBrainz $brainz;

    /**
     * @param array $genre
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $genre,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($genre['id']) ||
            !$this->hasValidId($genre['id'])
        ) {
            throw new Exception('Can not create genre object. Missing valid MBID');
        }

        $this->brainz         = $brainz;
        $this->data           = $genre;
        $this->id             = (string)$genre['id'];
        $this->name           = (string)($genre['name'] ?? '');
        $this->disambiguation = (string)($genre['disambiguation'] ?? '');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     disambiguation: string
     *  }
     */
    public function getProps(bool $includeData = false): array
    {
        $results = [
            'id' => $this->id,
            'name' => $this->name,
            'disambiguation' => $this->disambiguation,
        ];

        if ($includeData) {
            $results['data'] = $this->data;
        }

        return $results;
    }
}

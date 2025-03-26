<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\Alias;

/**
 * Represents a MusicBrainz label object
 */
class Label extends AbstractEntity implements EntityInterface
{
    public string $id;

    public string $name;

    public string $type;

    /** @var Alias[]|null $aliases */
    public ?array $aliases = null;

    public int $score;

    public string $sortName;

    public string $country;

    private array $data;

    protected MusicBrainz $brainz;

    /**
     * @param array $label
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $label,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($label['id']) ||
            !$this->hasValidId($label['id'])
        ) {
            throw new Exception('Can not create label object. Missing valid MBID');
        }

        $this->brainz   = $brainz;
        $this->data     = $label;
        $this->id       = (string)$label['id'];
        $this->type     = (string)($label['type'] ?? '');
        $this->score    = (int)($label['score'] ?? 0);
        $this->sortName = (string)($label['sort-name'] ?? '');
        $this->name     = (string)($label['name'] ?? '');
        $this->country  = (string)($label['country'] ?? '');
        $this->aliases  = $label['aliases'] ?? null;
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
     *     type: string,
     *     aliases: ?Alias[],
     *     score: int,
     *     sortName: string,
     *     country: string
     *  }
     */
    public function getProps(bool $includeData = false): array
    {
        $results = [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'aliases' => $this->aliases,
            'score' => $this->score,
            'sortName' => $this->sortName,
            'country' => $this->country,
        ];

        if ($includeData) {
            $results['data'] = $this->data;
        }

        return $results;
    }
}

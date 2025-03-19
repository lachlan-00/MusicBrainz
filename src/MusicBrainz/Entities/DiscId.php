<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz discid object
 * @package MusicBrainz
 */
class DiscId extends AbstractEntity implements EntityInterface
{
    public string $id;

    public int $offset_count;

    public int $sectors;

    /** @var int[] $offsets */
    public array $offsets;

    /** @var null|Release[] $releases */
    public ?array $releases = null;

    private array $data;

    private MusicBrainz $brainz;

    /**
     * @param array $discid
     * @param MusicBrainz $brainz
     * @throws Exception
     */
    public function __construct(
        array $discid,
        MusicBrainz $brainz
    ) {
        if (
            !isset($discid['id']) ||
            !$this->hasValidId($discid['id'])
        ) {
            throw new Exception('Can not create discid object. Missing valid MBID');
        }

        $this->brainz       = $brainz;
        $this->data         = $discid;
        $this->id           = $discid['id'];
        $this->offset_count = (int)($discid['offset-count'] ?? 0);
        $this->sectors      = (int)($discid['sectors'] ?? 0);
        $this->offsets      = $discid['offsets'] ?? [];
        $this->releases     = $discid['releases'] ?? null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return '';
    }

    public function getData(): array
    {
        return $this->data;
    }
}

<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use DateTime;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz release object
 * @package MusicBrainz
 */
class Release implements EntityInterface
{
    public string $id;

    public string $title;

    public string $status;

    public string $quality;

    public string $language;

    public string $script;

    public string $date;

    public string $country;

    public string $barcode;

    /** @var Artist[] */
    public array $artists = [];

    private array $data;

    protected MusicBrainz $brainz;

    protected ?DateTime $releaseDate = null;

    /**
     * @param array $release
     * @param MusicBrainz $brainz
     */
    public function __construct(
        array $release,
        MusicBrainz $brainz
    ) {
        $this->data   = $release;
        $this->brainz = $brainz;

        $this->id       = isset($release['id']) ? (string)$release['id'] : '';
        $this->title    = isset($release['title']) ? (string)$release['title'] : '';
        $this->status   = isset($release['status']) ? (string)$release['status'] : '';
        $this->quality  = isset($release['quality']) ? (string)$release['quality'] : '';
        $this->language = isset($release['text-representation']['language']) ? (string)$release['text-representation']['language'] : '';
        $this->script   = isset($release['text-representation']['script']) ? (string)$release['text-representation']['script'] : '';
        $this->date     = isset($release['date']) ? (string)$release['date'] : '';
        $this->country  = isset($release['country']) ? (string)$release['country'] : '';
        $this->barcode  = isset($release['barcode']) ? (string)$release['barcode'] : '';
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the earliest release date
     */
    public function getReleaseDate(): DateTime
    {
        if ($this->releaseDate !== null) {
            return $this->releaseDate;
        }

        // If there is no release date set, look through the release events
        if (
            !isset($this->data['date']) &&
            isset($this->data['release-events'])
        ) {
            return $this->getReleaseEventDates($this->data['release-events']);
        } elseif (isset($this->data['date'])) {
            return new DateTime($this->data['date']);
        }

        return new DateTime();
    }

    /**
     * @param array $releaseEvents
     * @return DateTime
     */
    public function getReleaseEventDates(array $releaseEvents): DateTime
    {

        $releaseDate = new DateTime();

        foreach ($releaseEvents as $releaseEvent) {
            if (isset($releaseEvent['date'])) {
                $releaseDateTmp = new DateTime($releaseEvent['date']);

                if ($releaseDateTmp < $releaseDate) {
                    $releaseDate = $releaseDateTmp;
                }
            }
        }

        return $releaseDate;
    }
}

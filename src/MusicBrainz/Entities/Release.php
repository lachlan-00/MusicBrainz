<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use DateTime;
use MusicBrainz\Exception;
use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz release object
 * @package MusicBrainz
 */
class Release extends AbstractEntity implements EntityInterface
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
     * @throws Exception
     */
    public function __construct(
        array $release,
        MusicBrainz $brainz,
    ) {
        if (
            !isset($release['id']) ||
            !$this->hasValidId($release['id'])
        ) {
            throw new Exception('Can not create release object. Missing valid MBID');
        }

        $this->brainz   = $brainz;
        $this->data     = $release;
        $this->id       = (string)$release['id'];
        $this->title    = (string)($release['title'] ?? '');
        $this->status   = (string)($release['status'] ?? '');
        $this->quality  = (string)($release['quality'] ?? '');
        $this->language = ($release['text-representation']->{'language'} ?? '');
        $this->script   = ($release['text-representation']->{'script'} ?? '');
        $this->date     = (string)($release['date'] ?? '');
        $this->country  = (string)($release['country'] ?? '');
        $this->barcode  = (string)($release['barcode'] ?? '');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array{
     *     id: string,
     *     title: string,
     *     status: string,
     *     quality: string,
     *     language: string,
     *     script: string,
     *     date: string,
     *     country: string,
     *     barcode: string
     * }
     */
    public function getProps(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'quality' => $this->quality,
            'language' => $this->language,
            'script' => $this->script,
            'date' => $this->date,
            'country' => $this->country,
            'barcode' => $this->barcode,
        ];
    }

    public function getTitle(): string
    {
        return $this->getName();
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

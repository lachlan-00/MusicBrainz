<?php

namespace MusicBrainz\Entities;

/**
 * Class EntityInterface
 * @package MusicBrainz\Entities
 */
interface EntityInterface
{
    public function getId(): string;

    public function getName(): string;

    public function getData(): array;

    /**
     * @return array<string, string|int|float|null>
     */
    public function getProps(): array;
}

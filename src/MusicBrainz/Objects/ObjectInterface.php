<?php

namespace MusicBrainz\Objects;

/**
 * Class EntityInterface
 * @package MusicBrainz\Entities
 */
interface ObjectInterface
{
    public function getData(): ObjectInterface;

    /**
     * @return array<string, string|int|float|null>
     */
    public function getProps(): array;
}

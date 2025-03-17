<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

use MusicBrainz\MusicBrainz;

/**
 * Represents a MusicBrainz coordinates object
 * @package MusicBrainz
 */
class Attributes
{
    public string $type_id;

    public string $type;

    public string $value;

    /**
     * @param array{
     *     type-id: ?string,
     *     type: ?string,
     *     value: ?string
     * } $attribute
     */
    public function __construct(
        array $attribute
    ) {
        $this->type_id = (string)($attribute['type-id'] ?? '');
        $this->type    = (string)($attribute['type'] ?? '');
        $this->value   = (string)($attribute['value'] ?? '');
    }
}

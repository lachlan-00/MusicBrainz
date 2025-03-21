<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

/**
 * Represents a MusicBrainz coordinates object
 * @package MusicBrainz
 */
class Attribute implements ObjectInterface
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

    /**
     * Get the object properties as an array
     *
     * @return array{
     *     type-id: string,
     *     type: string,
     *     value: string
     * }
     */
    public function getProps(): array
    {
        return [
            'type-id' => $this->type_id,
            'type' => $this->type,
            'value' => $this->value
        ];
    }
}

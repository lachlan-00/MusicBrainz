<?php

declare(strict_types=1);

namespace MusicBrainz\Objects;

/**
 * Represents a MusicBrainz Alias object
 * @package MusicBrainz
 */
class Alias implements ObjectInterface
{
    public string $name;

    public string $sort_name;

    public string $type;

    public string $type_id;

    public ?bool $primary = null;

    public ?string $begin_date = null;

    public ?string $end_date = null;

    /**
     * @param array{
     *      name: ?string,
     *      sort-name: ?string,
     *      type: ?string,
     *      type-id: ?string,
     *      primary: ?bool,
     *      begin-date: ?string,
     *      end-date: ?string
     * } $tag
     */
    public function __construct(
        array $tag
    ) {
        $this->name       = (string)($tag['name'] ?? '');
        $this->sort_name  = (string)($tag['sort-name'] ?? '');
        $this->type       = (string)($tag['type'] ?? '');
        $this->type_id    = (string)($tag['type-id'] ?? '');
        $this->primary    = (bool)($tag['primary'] ?? null);
        $this->begin_date = (string)($tag['begin-date'] ?? '');
        $this->end_date   = (string)($tag['end-date'] ?? '');
    }

    /**
     * @return array{
     *     name: string,
     *     sort-name: string,
     *     type: string,
     *     type-id: string,
     *     primary: ?bool,
     *     begin-date: ?string,
     *     end-date: ?string
     * }
     */
    public function getProps(): array
    {
        return [
            'name' => $this->name,
            'sort-name' => $this->sort_name,
            'type' => $this->type,
            'type-id' => $this->type_id,
            'primary' => $this->primary,
            'begin-date' => $this->begin_date,
            'end-date' => $this->end_date,
        ];
    }
}

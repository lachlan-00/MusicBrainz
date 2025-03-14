<?php

declare(strict_types=1);

namespace MusicBrainz\Entities;

use MusicBrainz\MusicBrainz;

/**
 * This is the abstract Entity class.
 * used for shared methods and properties.
 */
abstract class AbstractEntity
{
    public string $id;

    public function hasValidId(?string $mbid = ''): bool
    {
        return MusicBrainz::isMBID((string)$mbid);
    }
}

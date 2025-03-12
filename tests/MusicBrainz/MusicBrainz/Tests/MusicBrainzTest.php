<?php

namespace MusicBrainz\Tests;

use PHPUnit\Framework\TestCase;
use Guzzle\Http\ClientInterface;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;

class MusicBrainzTest extends TestCase
{
    protected MusicBrainz $brainz;

    protected function setUp(): void
    {
        $this->brainz = new MusicBrainz(new GuzzleHttpAdapter($this->createMock(ClientInterface::class)));
    }

    public static function MBIDProvider(): array
    {
        return [
            '4dbf5678-7a31-406a-abbe-232f8ac2cd63' => true,
            '35190110-9052-4c85-b635-a49bc16a4c74' => true,
            '4dbf5678-7a314-06aabb-e232f-8ac2cd63' => false, // invalid spacing for UUID's
            '4dbf5678-7a31-406a-abbe-232f8az2cd63' => false // z is an invalid character
        ];
    }

    public function testIsValidMBID(): void
    {
        foreach (self::MBIDProvider() as $mbid => $validation) {
            static::assertEquals($validation, $this->brainz->isValidMBID($mbid));
        }
    }
}

<?php

namespace MusicBrainz\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Guzzle\Http\ClientInterface;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;

/**
 * @covers MusicBrainz
 */
class MusicBrainzTest extends TestCase
{
    /**
     * @var MusicBrainz
     */
    protected $brainz;

    protected function setUp(): void
    {
        $this->brainz = new MusicBrainz(new GuzzleHttpAdapter($this->createMock(ClientInterface::class)));
    }

    /**
     * @return array{0: bool, 1: string}[]
     */
    public static function MBIDProvider()
    {
        return [
            [true, '4dbf5678-7a31-406a-abbe-232f8ac2cd63'],
            [true, '4dbf5678-7a31-406a-abbe-232f8ac2cd63'],
            [false, '4dbf5678-7a314-06aabb-e232f-8ac2cd63'], // invalid spacing for UUID's
            [false, '4dbf5678-7a31-406a-abbe-232f8az2cd63'] // z is an invalid character
        ];
    }

    /**
     * @dataProvider MBIDProvider
     */
    public function testIsValidMBID($validation, $mbid)
    {
        static::assertEquals($validation, $this->brainz->isValidMBID($mbid));
    }
}

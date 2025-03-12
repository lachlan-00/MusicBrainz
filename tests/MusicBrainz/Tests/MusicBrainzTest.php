<?php

namespace MusicBrainz\Tests;

use PHPUnit_Framework_TestCase;
use Guzzle\Http\ClientInterface;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;

/**
 * @covers MusicBrainz\MusicBrainz
 */
class MusicBrainzTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MusicBrainz
     */
    protected $brainz;

    protected function setUp()
    {
        /** @noinspection PhpParamsInspection */
        $this->brainz = new MusicBrainz(new GuzzleHttpAdapter($this->getMock(ClientInterface::class)));
    }

    /**
     * @return array
     */
    public function MBIDProvider()
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
        $this->assertEquals($validation, $this->brainz->isValidMBID($mbid));
    }
}

<?php

namespace MusicBrainz\Tests;

use GuzzleHttp\ClientInterface;
use MusicBrainz\Entities\Area;
use MusicBrainz\Entities\Artist;
use MusicBrainz\Entities\DiscId;
use MusicBrainz\Entities\Event;
use MusicBrainz\Entities\Place;
use MusicBrainz\Exception;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;
use MusicBrainz\Objects\LifeSpan;
use PHPUnit\Framework\TestCase;

/**
 * Entities are built from decoded web service responses, so every fixture here is a trimmed
 * copy of a real one and each case is a shape the service actually returns.
 */
class EntityTest extends TestCase
{
    private MusicBrainz $brainz;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->brainz = new MusicBrainz(new GuzzleHttpAdapter($this->createMock(ClientInterface::class)));
    }

    public function testArtistReadsALifeSpanThatOnlyHasABeginDate(): void
    {
        $artist = new Artist(
            [
                'id' => '4dbf5678-7a31-406a-abbe-232f8ac2cd63',
                'name' => 'Code 64',
                'life-span' => ['begin' => '2003'],
            ],
            $this->brainz
        );

        static::assertSame('2003', $artist->getBeginDate());
        static::assertNull($artist->getEndDate());
    }

    public function testArtistAcceptsALifeSpanDecodedAsAnObject(): void
    {
        $artist = new Artist(
            [
                'id' => '4dbf5678-7a31-406a-abbe-232f8ac2cd63',
                'name' => 'Code 64',
                'life-span' => (object)['begin' => '2003', 'end' => null],
            ],
            $this->brainz
        );

        static::assertSame('2003', $artist->getBeginDate());
        static::assertNull($artist->getEndDate());
    }

    public function testAreaBuildsALifeSpanObject(): void
    {
        $area = new Area(
            [
                'id' => '85752fda-13c4-31a3-bee5-0e5cb1f51dad',
                'name' => 'Sweden',
                'life-span' => (object)['begin' => null, 'end' => null, 'ended' => false],
            ],
            $this->brainz
        );

        static::assertInstanceOf(LifeSpan::class, $area->life_span);
        static::assertFalse($area->life_span->ended);
        static::assertSame(['ended' => false, 'end' => '', 'begin' => ''], $area->getProps()['life_span']);
    }

    public function testAreaWithoutALifeSpanReportsNull(): void
    {
        $area = new Area(
            ['id' => '85752fda-13c4-31a3-bee5-0e5cb1f51dad', 'name' => 'Sweden'],
            $this->brainz
        );

        static::assertNull($area->life_span);
        static::assertNull($area->getProps()['life_span']);
    }

    public function testEventBuildsALifeSpanObject(): void
    {
        $event = new Event(
            [
                'id' => '53bc3923-73f8-4e43-8bd6-1edf96e1b070',
                'name' => 'Wacken Open Air 2015',
                'life-span' => (object)['begin' => '2015-07-30', 'end' => '2015-08-01', 'ended' => true],
            ],
            $this->brainz
        );

        static::assertInstanceOf(LifeSpan::class, $event->life_span);
        static::assertSame('2015-07-30', $event->life_span->begin);
    }

    public function testPlaceBuildsItsAreaLifeSpanAndCoordinates(): void
    {
        $place = new Place(
            [
                'id' => 'd10077c1-03d9-4fbb-a038-6e35ada8eb9d',
                'name' => 'Fabrik',
                'area' => (object)['id' => '85752fda-13c4-31a3-bee5-0e5cb1f51dad', 'name' => 'Hamburg'],
                'life-span' => (object)['begin' => '2007-01-01', 'end' => null, 'ended' => false],
                'coordinates' => (object)['latitude' => 53.5, 'longitude' => 9.9],
            ],
            $this->brainz
        );

        static::assertInstanceOf(Area::class, $place->area);
        static::assertSame('Hamburg', $place->area->getName());
        static::assertInstanceOf(LifeSpan::class, $place->life_span);
        static::assertSame(53.5, $place->coordinates?->latitude);
    }

    public function testPlaceWithAnAreaThatHasNoMbidReportsNull(): void
    {
        $place = new Place(
            [
                'id' => 'd10077c1-03d9-4fbb-a038-6e35ada8eb9d',
                'name' => 'Fabrik',
                'area' => (object)['name' => 'Hamburg'],
            ],
            $this->brainz
        );

        static::assertNull($place->area);
        static::assertNull($place->coordinates);
    }

    public function testDiscIdAcceptsADiscIdRatherThanAnMbid(): void
    {
        $discId = new DiscId(
            [
                'id' => 'lwHl8fGzJyLXQR33ug60E8jhf4k-',
                'offset-count' => 2,
                'sectors' => 180000,
                'offsets' => [150, 90000],
            ],
            $this->brainz
        );

        static::assertSame('lwHl8fGzJyLXQR33ug60E8jhf4k-', $discId->getId());
        static::assertSame([150, 90000], $discId->offsets);
    }

    public function testDiscIdWithoutAnIdThrows(): void
    {
        static::expectException(Exception::class);

        new DiscId(['offset-count' => 2], $this->brainz);
    }

    public function testEntityWithoutAValidMbidThrows(): void
    {
        static::expectException(Exception::class);

        new Artist(['id' => 'not-an-mbid', 'name' => 'Code 64'], $this->brainz);
    }
}

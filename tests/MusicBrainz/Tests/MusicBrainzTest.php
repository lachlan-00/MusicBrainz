<?php

namespace MusicBrainz\Tests;

use GuzzleHttp\ClientInterface;
use MusicBrainz\Filters\ArtistFilter;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class MusicBrainzTest extends TestCase
{
    private MusicBrainz $brainz;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->brainz = new MusicBrainz(new GuzzleHttpAdapter($this->createMock(ClientInterface::class)));
    }

    /**
     * @return array<string, array{0: ?string, 1: bool}>
     */
    public static function MBIDProvider(): array
    {
        return [
            'lower case uuid' => ['4dbf5678-7a31-406a-abbe-232f8ac2cd63', true],
            'second lower case uuid' => ['35190110-9052-4c85-b635-a49bc16a4c74', true],
            'invalid spacing' => ['4dbf5678-7a314-06aabb-e232f-8ac2cd63', false],
            'z is not a hex character' => ['4dbf5678-7a31-406a-abbe-232f8az2cd63', false],
            'upper case is invalid but allowed by the library/API' => ['859A5c63-08DF-42DA-905C-7307F56DB95D', true],
            'null' => [null, false],
            'digits without separators' => ['44265678713140611225232681023463', false],
        ];
    }

    #[DataProvider('MBIDProvider')]
    public function testIsValidMBID(?string $mbid, bool $expected): void
    {
        static::assertSame($expected, $this->brainz->isValidMBID($mbid));
    }

    public function testCreateParametersBuildsALuceneQuery(): void
    {
        $params = (new ArtistFilter(['artist' => 'Weezer', 'country' => 'US']))->createParameters();

        static::assertSame('artist:Weezer+AND+country:US', $params['query']);
    }

    public function testCreateParametersDiscardsAnIncomingQuery(): void
    {
        $params = (new ArtistFilter(['artist' => 'Weezer']))->createParameters(['query' => 'artist:Nirvana']);

        static::assertSame('artist:Weezer', $params['query']);
    }

    public function testCreateParametersKeepsTheOtherParametersItWasGiven(): void
    {
        $params = (new ArtistFilter())->createParameters(['limit' => 25, 'offset' => null, 'fmt' => 'json']);

        static::assertSame('', $params['query']);
        static::assertSame(25, $params['limit']);
        static::assertSame('json', $params['fmt']);
    }
}

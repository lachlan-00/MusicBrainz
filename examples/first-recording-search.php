<pre><?php

use GuzzleHttp\Client;
use MusicBrainz\Entities\Recording;
use MusicBrainz\Filters\RecordingFilter;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;

require dirname(__DIR__) . '/vendor/autoload.php';

//Create new MusicBrainz object
$config = [
    'allow_redirects' => true,
    'verify' => false,
];
$client = new Client($config);
$brainz = new MusicBrainz(new GuzzleHttpAdapter($client));
$brainz->setUserAgent('ApplicationName', MusicBrainz::VERSION, 'https://example.com');

// set defaults
$releaseDate    = new DateTime();
$artistId       = null;
$songId         = null;
$trackLen       = -1;
$albumName      = '';
$lastScore      = null;
$firstRecording = [
    'release' => null,
    'releaseDate' => new DateTime(),
    'recording' => null,
    'artistId' => null,
    'recordingId' => null,
    'trackLength' => null
];

// Set the search arguments to pass into the RecordingFilter
$args = [
    "recording" => 'we will rock you',
    "artist" => 'Queen',
    'status' => 'official',
    'country' => 'GB'
];
try {
    // Find all the recordings that match the search and loop through them
    $recordings = $brainz->search(
        new RecordingFilter($args),
        1
    );

    /** @var $recording Recording */
    foreach ($recordings as $recording) {

        // if the recording has a lower score than the previous recording, stop the loop.
        // This is because scores less than 100 usually don't match the search well
        if (
            $lastScore !== null &&
            $recording->getScore() < $lastScore
        ) {
            break;
        }

        $lastScore        = $recording->getScore();
        $releaseDates     = $recording->getReleaseDates();
        $oldestReleaseKey = key($releaseDates);

        if (strtoupper($recording->getArtist()->getName()) == strtoupper($args['artist'])
            && $releaseDates[$oldestReleaseKey] < $firstRecording['releaseDate']
        ) {

            $firstRecording = [
                'release' => $recording->releases[$oldestReleaseKey],
                'releaseDate' => $recording->releases[$oldestReleaseKey]->getReleaseDate(),
                'recording' => $recording,
                'artistId' => $recording->getArtist()->getId(),
                'recordingId' => $recording->getId(),
                'trackLength' => $recording->getLength('long')
            ];
        }
    }

    var_dump([$firstRecording]);
} catch (Exception $e) {
    print($e->getMessage());
}

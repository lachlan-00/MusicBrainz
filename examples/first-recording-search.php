<pre><?php

use MusicBrainz\Entities\Recording;
use MusicBrainz\MusicBrainz;

require dirname(__DIR__) . '/vendor/autoload.php';

//Create new MusicBrainz object
$config = [
    'allow_redirects' => true,
    'verify' => false,
];

$username = null;
$password = null;
$brainz   = MusicBrainz::newMusicBrainz('guzzle', $username, $password, null, $config);
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
    'recording' => 'we will rock you',
    'artist' => 'Queen',
    'status' => 'official',
    'country' => 'GB'
];
try {
    // Find all the recordings that match the search and loop through them
    $search = $brainz->search(
        MusicBrainz::newFilter('recording', $args),
        1,
    );

    /** @var $recording Recording */
    foreach ($search as $recording) {
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

        if (
            strtoupper($recording->getArtist()->getName()) == strtoupper($args['artist']) &&
            $releaseDates[$oldestReleaseKey]->getTimestamp() < $firstRecording['releaseDate']->getTimestamp()
        ) {
            $firstRecording = [
                'release' => $recording->releases[key($releaseDate)],
                'releaseDate' => $recording->releases[key($releaseDate)]->getReleaseDate(),
                'recording' => $recording,
                'artistId' => $recording->getArtist()->getId(),
                'recordingId' => $recording->getId(),
                'trackLength' => $recording->getLength('long')
            ];
        }
    }

    if ($firstRecording['release'] === null) {
        throw new Exception('No search results found');
    }

    print_r([$firstRecording]);
} catch (Exception $e) {
    print($e->getMessage());
}

# MusicBrainz Web Service (v2) PHP class

This PHP library that allows you to easily access the MusicBrainz Web Service V2 API. Visit the [MusicBrainz development page](http://musicbrainz.org/doc/Development) for more information.

This project is a fork of [chrisdawson/MusicBrainz](https://github.com/chrisdawson/MusicBrainz) and takes some inspiration from the [Python bindings](https://github.com/alastair/python-musicbrainz-ngs)

As of 2025-03-12 the project has been forked again from [mikealmond/MusicBrainz](https://github.com/mikealmond/MusicBrainz)

## Usage Example

```php
<?php
    use Guzzle\Http\Client;
    use MusicBrainz\Filters\ArtistFilter;
    use MusicBrainz\Filters\RecordingFilter;
    use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
    use MusicBrainz\MusicBrainz;

    require __DIR__ . '/vendor/autoload.php';

    // Create new Guzzle HTTP client
    $config = [
        'allow_redirects' => true
    ];
    $client = new Client($config);

    // Some areas require authentication
    $username = 'username';
    $password = 'password';

    // Create new MusicBrainz object
    $brainz   = new MusicBrainz(new GuzzleHttpAdapter($client), $username, $password);
    $brainz->setUserAgent('ApplicationName', MusicBrainz::VERSION, 'https://example.com');

    try {
        // Search for Buddy Holly recordings by Weezer
        $args = array(
            "recording"  => "Buddy Holly",
            "artist"     => 'Weezer',
            "creditname" => 'Weezer',
            "status"     => 'Official'
        );

        // Search for recordings and then return a list of Recording objects
        $recordings = $brainz->search(
            new RecordingFilter($args)
        );
        print_r($recordings);
    } catch (Exception $e) {
        print $e->getMessage();
    }
    try {
        // Search for an artist by the recording ID, include the aliases, ratings and genres
        $includes = ['aliases', 'ratings', 'genres'];
        $browse   = $brainz->browseArtist(
            'recording',
            'd615590b-1546-441d-9703-b3cf88487cbd',
            $includes,
            1
        );

        // Filters are used to parse responses and return data objects    
        $artistFilter = new ArtistFilter([]);
        $genreFilter  = new GenreFilter([]);

        // artistFilter::parseResponse returns an array of Artist objects
        foreach ($artistFilter->parseResponse($browse, $brainz) as $artist) {
            // print Artist data property
            print_r($artist->getData());

            // genreFilter::parseResponse returns an array of Genre objects
            foreach ($genreFilter->parseResponse($artist->getData(), $brainz) as $genre) {
                // print each genre for the artist
                print_r($genre->getData());
            }
        }
    } catch (Exception $e) {
        print $e->getMessage();
    }
```

Look in the [/examples](https://github.com/mikealmond/MusicBrainz/tree/master/examples) folder for more.

## Requirements

PHP8.2+ and [cURL extension](http://php.net/manual/en/book.curl.php).

You must also choose a HTTP client to use.

They are not included in the composer requirements to allow you to choose your own based on your project requirements.

Add it to your composer requirements with composer require.

* The library is built to use
  * rmccue/requests
  * guzzlehttp/guzzle
  * guzzle/guzzle (deprecated)

## License

### Short

Use it in any project, no matter if it is commercial or not. Just don't remove the copyright notice.

### MIT License

Copyright © 2015 Mike Almond

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

# MusicBrainz

## 0.3.1

### Fixed

* Fixed typing for `array|object` returns

## 0.3.0

First fork release updating the code to require PHP8.2 minimum.

No functional changes but many code quality updates and strict typing is enabled.

### Added

* CHANGELOG.md
* Suggest ext-curl in composer.json
* Dev tools added to composer.json
  * php-cs-fixer
  * phpstan
  * rector
* Composer commands to run dev tools
* Function and parameter typing

### Changed

* Redirect repository information from [mikealmond](https://github.com/mikealmond/musicbrainz) to [lachlan-00](https://github.com/lachlan-00/musicbrainz)
* MusicBrainz::isValidMBID returns bool only
* Added requests and guzzle to dev requirements instead of suggestions
* Strict typing enforced `declare(strict_types=1);`
* Update phpunit and fixed up tests for newer version
* Set initial userAgent property from a VERSION constant

### Removed

* PHP support less that PHP8.2
* GitHub funding link

### Fixed

* Missing countries in the countries data array
* Dynamic property declarations
* Old Requests import method causing deprecation warnings
* [fix search, add missing entities](https://github.com/lachlan-00/MusicBrainz/pull/1)
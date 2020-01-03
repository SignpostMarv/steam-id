# signpostmarv/steam-id
[![Test Status](https://github.com/SignpostMarv/steam-id/workflows/Tests/badge.svg)](https://github.com/SignpostMarv/steam-id/actions) [![Code Coverage](https://coveralls.io/repos/github/SignpostMarv/steam-id/badge.svg?branch=master)](https://coveralls.io/github/SignpostMarv/steam-id?branch=master) ![Type Coverage](https://shepherd.dev/github/SignpostMarv/steam-id/coverage.svg)

A library for parsing [Steam IDs](https://developer.valvesoftware.com/wiki/SteamID)

## Requirements
* php-7.4
* ext-ctype

## Install
`composer require signpostmarv/steam-id`

### Suggested Steps

#### Use vimeo/psalm
Psalm saves on implementing runtime checks that can be detected with static analysis.

For example, [at the time of writing](https://developer.valvesoftware.com/w/index.php?title=SteamID&oldid=228298) an ID's Universe component does not go higher than 5 but uses an 8-bit field, but the following code will fail to pass a check on psalm:
```php
new \SignpostMarv\SteamId\SteamId(0, 0, 0, 0, 6);
```
> ERROR: InvalidArgument - Argument 5 of SignpostMarv\SteamId\SteamId::__construct expects int(0)|int(1)|int(2)|int(3)|int(4)|int(5), int(6) provided

* [Install vimeo/psalm](https://psalm.dev/docs/running_psalm/installation/)
* [Configure psalm to test your source](https://psalm.dev/docs/running_psalm/configuration/#ltprojectfilesgt)

### Optional Steps
Use either the [BCMath](https://github.com/signpostmarv/brick-math-base-convert-bcmath) or [GMP](https://github.com/signpostmarv/brick-math-base-convert-gmp) calculators if the relevant php extension is available.

## Examples

### Obtain Steam Community URL
```php
use SignpostMarv\SteamId\Parser;

$parser = new Parser();

$id = $parser->FromString('76561197960287930');

$url = $parser->ToSteamCommunityUrl($id);
```

## License
Apache-2.0

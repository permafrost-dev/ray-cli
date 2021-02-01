<p align="center">
<img src="https://static.permafrost.dev/images/ray-cli/ray-cli-logo-01.png" alt="Permafrost Dev" height="100" style="block">
<br><br>
  <code style="font-size:2.2rem;"><strong>ray-cli</strong></code>
</p>

<p align="center">
<img src="https://img.shields.io/packagist/v/permafrost-dev/ray-cli" alt="version"/>  <img src="https://img.shields.io/packagist/l/permafrost-dev/ray-cli" alt="license"/> <img src="https://img.shields.io/packagist/dt/permafrost-dev/ray-cli?logo=packagist" alt="downloads"/> <img src="https://img.shields.io/github/workflow/status/permafrost-dev/ray-cli/Run%20Tests/main?logo=github" alt="Run Tests"/> <img src="https://shields.io/coveralls/github/permafrost-dev/ray-cli" alt="Coverage Status" />
</p>

This package provides a command-line interface for interacting with the [Ray](https://myray.app) application by [Spatie](https://github.com/spatie).

---

Supported PHP versions: `7.4`, `8.0`.

## Installation

You may install the package using composer:

`composer require permafrost-dev/ray-cli --dev`

Also available is a download for  a `phar` executable from the [releases](https://github.com/permafrost-dev/ray-cli/releases) page.  The primary advantage of using a `phar` is that **you don't need to install the package** into your project.

If you download  a `phstringar`, replace _`vendor/bin/ray`_ with the filename of the `phar` in the examples, i.e.:

```bash
vendor/bin/ray 'test message' --green --large
```

_would become:_

```bash
ray-1.6.0.phar 'test message' --green --large
```

_Note: Some users may need to set the phar as executable using `chmod`. Example: `chmod +x ray-1.6.0.phar`_

---

## Usage

Sending data to Ray is as simple as calling the `ray` script and providing a single argument, either a string or a filename:

`vendor/bin/ray 'hello world'` 

You can provide a JSON string and Ray will format it for you:

`vendor/bin/ray '{"message": "hello world"}'`

<p align="center">
    <img src="https://static.permafrost.dev/images/ray-cli/json-decoded.png" alt="Decoded JSON" height="200" style="block">
  </p>

You're also able to pass a valid filename instead of a string. The contents of the file will be sent instead, with automatic JSON detection.

```bash
vendor/bin/ray "testfile.json" -c green
vendor/bin/ray "readme.txt"
```

You can even pass a valid URL - it will be downloaded and sent to Ray, with automatic JSON detection.

```bash
vendor/bin/ray "https://github.com/permafrost-dev/ray-cli" -c green
vendor/bin/ray "https://api.github.com/repos/permafrost-dev/ray-cli"
```

---

## Available Options

The `ray` script offers several flags for sending additional payloads to Ray:

### `--exec`

Arguments: `none`

Default: `false`

Description: Treats the argument as a script or executable, executes it, and sends the output to Ray.  Supported interpreters for scripts are `PHP`, `Python`, and `NodeJS`.  If the file is executable, such as a binary or `.sh` script, it will also be executed.  JSON content is automatically detected.

Example:

```bash
# refresh the display of app.log every 5 seconds
vendor/bin/ray --exec "random.sh"
vendor/bin/ray --exec "random-number.php"
```

### `--refresh`

Arguments: `integer` or `decimal`

Default: `none`

Description: Refreshes the payload display in Ray every N seconds, where N is either a whole number (i.e., `10`) or a decimal (i.e., `7.5`).  If a file is specified, it is re-read every N seconds; if a URL is specified, it is re-retrieved.

Example:

```bash
# refresh the display of app.log every 5 seconds
vendor/bin/ray --refresh=5 "storage/logs/app.log"
# ...or refresh every 2.5 seconds
vendor/bin/ray --refresh=2.5 "storage/logs/app.log"
```

### `--color` or `-c`

Arguments: `string`

Default: `none`

Description: sends a "color" payload along with the data.

Example:

```bash
vendor/bin/ray -c red "hello world"
```

### `--large` or `--lg`

Arguments: `none`

Default: `false`

Description: sends the payload as large-sized text.

Example:

```bash
vendor/bin/ray --large "hello world"
vendor/bin/ray --lg "hello world"
```

### `--small`or `--sm`

Arguments: `none`

Default: `false`

Description: sends the payload as small-sized text.

Example:

```bash
vendor/bin/ray --small "hello world"
vendor/bin/ray --sm "hello world"
```

### `--size` or `-S`

Arguments: `string`

Default: `normal`

Possible Values: `large`, `lg`, `small`, `sm`, `normal`

Description: sends a payload with the specified text size.  See `--large` and `--small`.  _Note that while included for completeness, specifying `normal` is not necessary as it is the default text size._

Example:

```bash
vendor/bin/ray -S sm "hello world"
vendor/bin/ray -S large "hello world"
vendor/bin/ray --size=normal "hello world"
```

### `--label` or `-L`

Arguments: `string`

Default: `none`

Description: sends a "label" payload along with the data.  Only works when sending plain _(non-JSON and non-delimited)_ strings.

Example:

```bash
vendor/bin/ray -L "my label" "hello world"
```

### `--notify` or `-N`

Arguments: `none`

Default: `false`

Description: sends a "notification" payload, causing Ray to display an OS notification instead of logging the data in its window.

Example:

```bash
vendor/bin/ray -N "hello from ray-cli"
```

### `--csv`

Arguments: `none`

Default: `false`

Description: causes the payload data to be treated as a comma-separated list of values, and will `explode()` the data and send the resulting array of values instead.

Example:

```bash
vendor/bin/ray --csv "one,two,three"
```

### `--delimiter` or `-D`

Arguments: `string`

Default: `none`

Description: causes the payload data to be treated as a list of values delimited by the provided delimiter string, and will `explode()` the data and send the resulting array of values instead.

Example:

```bash
vendor/bin/ray -D '|' "one|two|three"
```

### `--json` or `-j`

Arguments: `none`

Default: `false`

Description: Forces the payload data to be treated as a JSON string. Note that this flag is unnecessary in most cases because JSON strings are automatically detected.

Example:

```bash
vendor/bin/ray --json '["one","two","three"]'
```

### `--stdin`

Arguments: `none`

Default: `false`

Description: Reads the payload data from the standard input instead of as a command line parameter.  Note that the payload data can be specified as as dash _(`"-"`)_ instead of specifying the `--stdin` flag.

Example:

```bash
echo "hello world" | vendor/bin/ray --stdin
echo "hello world" | vendor/bin/ray -
```

### `--raw`

Arguments: `none`

Default: `false`

Description: Forces the payload data be pre-processed and to display the raw, unrendered content.  The data is processed to encode HTML entities, spaces, and converts new lines to `<br>` tags _(this is done to display HTML source code)_.

Example:

```bash
cat sample.html | vendor/bin/ray --stdin --raw
vendor/bin/ray --raw sample.html
```

### `--screen` or `-s`

Arguments: `string`

Default: `none`

Description: causes a new screen to be created in Ray, with the argument being the "name" of the new screen.  Passing an empty string or a string value of `"-"` will cause the screen to be unnamed _(the same effect as calling `ray()->clearScreen()`)_.  Passing `--screen` or `-s` as the last argument on the command line is the same as providing a screen name of `"-"`.

Example:

```bash
# create a screen named "debug #1":
vendor/bin/ray -s 'debug #1' "hello world"
vendor/bin/ray --screen='debug #1' "hello world"

# create a screen with no name:
vendor/bin/ray -s- "hello world"
vendor/bin/ray --screen=- "hello world"
vendor/bin/ray --screen= "hello world"
vendor/bin/ray "hello world" -s

# create a named screen without sending data:
vendor/bin/ray --screen="my screen 2"
vendor/bin/ray -s "my screen 3"
```

### `--clear` or `-C`

Arguments: `none`

Default: `none`

Example:
Description: causes Ray the clear the screen _(it's really just creating a new screen with no name)_. **If both `--screen` and `--clear` are provided, `--clear` takes precedence.**

Example:

```bash
# clear the screen and send some data:
vendor/bin/ray -C "hello world"
vendor/bin/ray --clear "hello world"

# clear the screen without sending any data:
vendor/bin/ray -C
vendor/bin/ray --clear
```

### `--clear-all`

Arguments: `none`

Default: `none`

Description: causes Ray to clear all screens.

Example:

```bash
vendor/bin/ray --clear-all
```

### `--image` or `-i`

Arguments: `none`

Default: `none`

Description: causes the payload to be treated as an image.  The payload must be either a URL or an existing filename.

Example:

```bash
vendor/bin/ray --image https://static.permafrost.dev/images/ray-cli/ray-cli-logo-01.png

vendor/bin/ray -i my-image-file.png
```

## `--blue`, `--gray`, `--green`, `--orange`, `--purple`, `--red`

Arguments: `none`

Default: `false`

Description: causes the payload to be sent with the indicated color.  Alias for  the `--color=N` flag.

Example:

```bash
vendor/bin/ray --red "hello world"
vendor/bin/ray --orange "hello world"

# only the first flag is used when multiple flags are provided.
# sent as green:
vendor/bin/ray --green --red --blue "hello world"
```

## `--bg-blue`, `--bg-gray`, `--bg-green`, `--bg-orange`, `--bg-purple`, `--bg-red`

Arguments: `none`

Default: `false`

Description: causes the payload to be sent with the indicated background color.

Example:

```bash
vendor/bin/ray --bg-purple --large "hello world"
```

<p align="center">
    <img src="https://static.permafrost.dev/images/ray-cli/with-background-color.png" alt="Purple Background" height="200" style="block">
  </p>

---

## Examples

Send the contents of a JSON file to Ray with a blue marker:

```bash
cat my-data.json | vendor/bin/ray --stdin -c blue
vendor/bin/ray 'my-data.json' --blue
```

Send the contents of `test.json` with small text, a red marker, and to a new screen named "my data": 

```php
vendor/bin/ray --screen='my data' --red --small 'test.json'
```

---

## Testing

This package uses PHPUnit for unit tests.  To run the test suite, run:

`./vendor/bin/phpunit`

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

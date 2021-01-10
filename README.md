<p align="center">
<img src="https://static.permafrost.dev/images/ray-cli/ray-cli-logo-01.png" alt="Permafrost Dev" height="100" style="block">
<br><br>
  <code style="font-size:2.2rem;"><strong>ray-cli</strong></code>
</p>

<p align="center">
<img src="https://img.shields.io/packagist/v/permafrost-dev/ray-cli" alt="version"/> <img src="https://img.shields.io/packagist/l/permafrost-dev/ray-cli" alt="license"/> <img src="https://img.shields.io/packagist/dt/permafrost-dev/ray-cli" alt="downloads"/> <img src="https://img.shields.io/github/workflow/status/permafrost-dev/ray-cli/Run%20Tests/main" alt="Run Tests"/>
</p>

<br>

This package provides a command-line interface for interacting with the [Ray](https://myray.app) application by [Spatie](https://github.com/spatie).

---

Supported PHP versions: `7.4`, `8.0`.

## Installation

`composer require permafrost-dev/ray-cli --dev`

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

---

## Available Options

The `ray` script offers several flags for sending additional payloads to Ray:

### `--color` or `-c`

Arguments: `string`

Default: `none`

Description: sends a "color" payload along with the data.

Example:

```bash
vendor/bin/ray -c red "hello world"
```

### `--label` or `-L`

Arguments: `string`

Default: `none`

Description: sends a "label" payload along with the data.  Only works when sending plain (non-JSON) strings.

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

Description: Reads the payload data from the standard input instead of as a command line parameter.

Example:

```bash
echo "hello world" | vendor/bin/ray --stdin
```

---

## Examples

Send the contents of a JSON file to Ray with a blue marker:

```bash
cat my-data.json | vendor/bin/ray --stdin -c blue
vendor/bin/ray 'my-data.json' -c blue
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

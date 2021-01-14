# Available Options

The `ray-cli` application offers a number options for sending information to `Ray`, including some extra options not available by default with the available SDKs.

_Most options have a long and short form; the long option must be in the format `--name=value`, and the short option should be formatted as `-x value`._

---

## Colors

### Flag: `--color` or `-c`

Arguments: `string`

Default: `none`

Accepted Values: `blue`, `gray`, `green`, `orange`, `purple`, `red`

Description: sets the payload color and displays a colored marker in Ray alongside the data.

Examples: 

- `vendor/bin/ray -c red "hello world"`

### Flag: `--blue`, `--gray`, `--green`, `--orange`, `--purple`, `--red`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: Sends the payload with the indicated color.  Alias for  the `--color` flag.

Examples:

- `vendor/bin/ray --red "hello world"`

### Flag: `--bg-blue`, `--bg-gray`, `--bg-green`, `--bg-orange`, `--bg-purple`, `--bg-red`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: Sends the payload with the indicated background color. This is a non-standard option and only available in `ray-cli`.

Examples:

- `vendor/bin/ray --bg-purple --large "hello world"`

![Background Color](https://static.permafrost.dev/images/ray-cli/with-background-color.png)

---

## Sizes

### Flag: `--large`, `--lg`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: sends the payload as large-sized text.

Examples:

- `vendor/bin/ray --large "hello world"`

### Flag: `--small`, `--sm`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: sends the payload as small-sized text.

Examples: 

- `vendor/bin/ray --small "hello world"`

### Flag: `--size`, `-S`

Arguments: `string`

Default: `normal`

Accepted Values: `large`, `lg`, `small`, `sm`, `normal`

Description: Sends a payload with the specified text size.  See `--large` and `--small`.  _Specifying `normal` is not necessary as it is the default text size._

Examples:

- `vendor/bin/ray -S sm "hello world"`
- `vendor/bin/ray -S large "hello world"`
- `vendor/bin/ray --size=lg "hello world"`

---

## Payloads

### Flag: `--csv`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: Causes the payload data to be treated as a comma-separated list of values, and will `explode()` the data and send the resulting array of values instead.

Examples:

- `vendor/bin/ray --csv "one,two,three"`

### Flag: `--delimiter`, `-D`

Arguments: `string`

Default: `none`

Accepted Values: `any string value`

Description: Causes the payload data to be treated as a list of values delimited by the provided delimiter string, and will `explode()` the data and send the resulting array of values instead.

Examples:

- `vendor/bin/ray -D '|' "one|two|three"`
- `ls -1 | vendor/bin/ray -D '\n' --stdin`

### Flag: `--label`, `-L`

Arguments: `string`

Default: `none`

Accepted Values: `any valid string`

Description: Sends a "label" payload along with the data.  Only works when sending plain _(non-JSON and non-delimited)_ strings.

Examples:

- `vendor/bin/ray -L "my label" "hello world"`

---

## Modifiers

### Flag: `--notify`, `-N`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: Sends a "notification" payload, causing Ray to display an OS notification instead of logging the data in its window.

Examples:

- `vendor/bin/ray -N "hello from ray-cli"`

### Flag: `--json`, `-j`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: Forces the payload data to be treated as a JSON string.  _Note that this flag is unnecessary in most cases because **JSON strings are automatically detected**._

Examples:

- `vendor/bin/ray --json '["one","two","three"]'`

### Flag: `--raw`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: Forces the payload data to skip pre-processing.  Normally, the data is processed to encode HTML entities, spaces, and converts new lines to `<br>` tags _(this is done to display HTML source code)_.

Examples:

- `cat sample.html | vendor/bin/ray --stdin --raw`

---

## Screens

### Flag: `--screen`, `-s`

Arguments: `string`, `optional`

Default: `none`

Accepted Values: `any valid string`, `a dash "-"`, `none`

Description: causes a new screen to be created in Ray, with the argument being the "name" of the new screen.  Passing an empty string or a string value of `"-"` will cause the screen to be unnamed _(the same effect as calling `ray()->clearScreen()`)_.  Passing `--screen` or `-s` as the last argument on the command line is the same as providing a screen name of `"-"`.

Examples:

- `vendor/bin/ray -s 'debug #1' "hello world"`
- `vendor/bin/ray -s- "hello world"`
- `vendor/bin/ray "hello world" -s`
- `vendor/bin/ray --screen="my screen 3"`

### Flag: `--clear`, `-C`

Arguments: `none`

Default: `none`

Accepted Values: `none`

Description: Causes Ray the clear the screen _(it's really just creating a new screen with no name)_. **If both `--screen` and `--clear` are provided, `--clear` takes precedence.**

Examples:

- `vendor/bin/ray --clear "hello world"`
- `vendor/bin/ray -C`

---

## Misc Options

### Flag: `--stdin`

Arguments: `none`

Default: `false`

Accepted Values: `none`

Description: Reads the payload data from the standard input instead of as a command line parameter.  Note that the payload data can be specified as as dash _(`"-"`)_ instead of specifying the `--stdin` flag.

Examples:

- `echo "hello world" | vendor/bin/ray --stdin`
- `cat demo.html | vendor/bin/ray -`

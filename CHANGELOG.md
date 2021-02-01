# Changelog

All notable changes to `permafrost-dev/ray-cli` will be documented in this file.

---

## 1.11.0 - 2021-02-01

- add `--image` and `--clear-all` flags

- add `--update-check` flag to force `ray-cli` to check for an updated version

- `ray-cli` now returns silently unless the `-v`/`--verbose` flag is specified

## 1.10.1 - 2021-01-15

- disable update checks due to bug with version_compare().

## 1.10.0 - 2021-01-15

- add `--refresh=N` option that refreshes the payload display in Ray every N seconds.  If the payload argument is a file, the contents are re-read every N seconds; if it's a URL, it is re-downloaded every N seconds.

- add `--exec` option that will treat the main parameter as a script or executable filename.  The file will be executed and the output will be sent to Ray.  Supported script interpreters are `PHP`, `Python`, and `NodeJS`.  All files marked as executable are supported.

## 1.9.0 - 2021-01-14

- every time `ray-cli` is executed, there's a 25% chance to make an http call to the github api to check for a new release.  This is done to avoid sending too many http requests during frequent use.  If there is a new release, a message is displayed along with a link where the release can be downloaded.

- change the way that `--raw` works so that it displays the raw content of a file, even if it is html (previously it worked in the reverse, which didn't make sense).

- fix a few minor bugs

## 1.8.1 - 2021-01-14

- minor change to the way the url request method is set

- minor tweaks to unit tests for url client

## 1.8.0 - 2021-01-14

- when a url is passed as the data argument, it is retrieved and the results are displayed in Ray.  JSON data is automatically detected.

- code cleanup

- increased test coverage

## 1.7.0 - 2021-01-13

- add `--bg-<color>` flags to specify the background color of the payload.  Available colors are: blue, gray, green, orange, purple, red.

- code cleanup

## 1.6.0 - 2021-01-12

- add `--raw` flag to send contents without any pre-processing, such as sending the contents of an html file _(without `--raw`, the source will display instead)

- specifying the payload as  as dash `"-"` is now treated as an indicator that data should be read from `stdin` _(this is the same as specifying `--stdin`)._

- code cleanup & reorganization

## 1.5.0 - 2021-01-12

- add `--sm` and `--lg` flags as shorter versions of `--small` and `--large`

- add `--size <size>` flag to set the payload text size

- add scripts used for building release phar executable

- update github action workflows to only trigger when appropriate

- update readme with example usage for `phar` executable

- fix readme typos

## 1.4.0 - 2020-01-11

- add color name flags *(`--red`, `--blue`, etc.)*

- major update to tests, increasing coverage considerably

- add support for coveralls.io _(config file, github action workflow)_

- fix typo in PHPUnit configuration

- update readme with examples of color name flags

## 1.3.2 - 2020-01-11

- fix bug in `Utilities::initializeCommand()`

## 1.3.1 - 2021-01-11

- ensure data is only sent to Ray when appropriate

- code optimization, reorganization, cleanup

- add additional code for building phar

- set the application version _(displayed with `ray --version`)_

## 1.3.0 - 2021-01-11

- add `--large` flag to allow sending payloads as large-sized text

- add `--small` flag to allow sending payloads as small-sized text

- update readme with examples of `--large` and `--small`

- fix minor bugs related to `--clear` and `--screen`

## 1.2.0 - 2021-01-11

- add `--clear|-C` flag to allow clearing the screen in Ray

- add `--screen|-s` flag to allow creating a new named screen in Ray

- code cleanup

- add some separation of concerns in code

## 1.1.1 - 2021-01-10

- update readme with examples on filename arguments, minor other changes

## 1.1.0 - 2021-01-10

- fix autodetection of JSON strings
- if a valid filename is passed as the only argument, the content of the file is sent to Ray (JSON is autodetected)

## 1.0.3 - 2021-01-10

- update readme
- fix --notify flag not sending notification payload

## 1.0.2 - 2021-01-10

- update readme

## 1.0.0 - 2021-01-10

- initial release

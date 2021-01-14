# Changelog

All notable changes to `permafrost-dev/ray-cli` will be documented in this file.

---

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

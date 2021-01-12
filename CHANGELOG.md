# Changelog

All notable changes to `permafrost-dev/ray-cli` will be documented in this file.

---

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

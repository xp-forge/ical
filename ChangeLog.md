iCal ChangeLog
==============

## ?.?.? / ????-??-??

## 4.0.0 / 2021-10-24

* Implemented xp-framework/rfc#341, dropping compatibility with XP 9
  (@thekid)
* Merged PR #6: Remove dependency on xp-forge/partial library - @thekid

## 3.0.0 / 2020-04-10

* Implemented xp-framework/rfc#334: Drop PHP 5.6:
  . **Heads up:** Minimum required PHP version now is PHP 7.0.0
  . Rewrote code base, grouping use statements
  . Rewrote `isset(X) ? X : default` to `X ?? default`
  (@thekid)

## 2.1.2 / 2020-04-04

* Made compatible with XP 10 - @thekid

## 2.1.1 / 2018-10-16

* Fixed line length not to exceed 75 characters - @thekid

## 2.1.0 / 2018-03-30

* Fixed PHP 7.2 compatibility by renaming the text.ical.Object interface
  to `IObject` (since https://wiki.php.net/rfc/object-typehint, value
  types cannot be named "object" anymore)
  (@thekid)
* Fixed problem when END token comes before BEGIN - @thekid
* Fixed issue #3: NPE for empty input - @thekid

## 2.0.0 / 2017-06-04

* Added forward compatibility with XP 9.0.0 - @thekid

## 1.0.0 / 2017-05-01

* Added support for XP8 - @thekid
* Added support for PHP 7.1 - @thekid

## 0.9.1 / 2017-01-12

* Fixed escaping for commas, semicolons and backslashes - @thekid

## 0.9.0 / 2016-06-26

* Fixed issue #2: Date and time formats - @thekid

## 0.8.0 / 2016-06-26

* Added type information to all objects - @thekid
* Added support for named properties (e.g. `X-MICROSOFT-CDO-*`) to
  calendars, events and alarms.
  (@thekid)

## 0.7.0 / 2016-06-26

* Added support for multiple events inside a calendar
  (@thekid)
* Added dtstamp, priority, transp and sequence fields to event
  (@thekid)

## 0.6.0 / 2016-06-26

* Add checks to detect illegal nesting - e.g. an calendar inside an
  event or an event inside an event.
  (@thekid)
* Changed reader to only allow `VCALENDAR` as root-level object
  (@thekid)

## 0.5.0 / 2016-06-26

* Introduced class constants:
  - `text.ical.Method` for calendar methods, e.g. *REQUEST* or *REPLY*
  - `text.ical.Role` for roles, e.g. *CHAIR* or *REQ-PARTICIPANT*.
  - `text.ical.Type` for types, e.g. *INDIVIDUAL* or *ROOM*.
  - `text.ical.PartStat` for status, e.g. *NEEDS-ACTION* or *TENTATIVE*
  (@thekid)

## 0.4.0 / 2016-06-26

* Wrapped lines after 75 characters. The implementation will keep pairs
  together and use `wordwrap()` for the content, splitting long words
  when necessary - issue #1, part 3
  (@thekid)
* Support line continuation via TAB characters, too - issue #1, part 2
  (@thekid)
* Changed output to always use `\r\n` as mandated by spec. See issue #1
  (@thekid)

## 0.3.0 / 2016-06-26

* **Heads up**: Renamed everything to **ical**: iCalendar is the successor
  of vCalendar, see https://en.wikipedia.org/wiki/ICalendar#vCalendar_1.0
  (@thekid)

## 0.2.0 / 2016-06-24

* Implemented `VALARM` - @thekid

## 0.1.0 / 2016-06-24

* Hello World! First release - @thekid
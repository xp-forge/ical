iCal: Calendar and events
=========================

[![Build status on GitHub](https://github.com/xp-forge/ical/workflows/Tests/badge.svg)](https://github.com/xp-forge/ical/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/ical/version.png)](https://packagist.org/packages/xp-forge/ical)

I/O
---
Calendars can be read and written using the ICalendar class

```php
use text\ical\ICalendar;
use util\cmd\Console;
use io\File;

$ical= new ICalendar();

$calendar= $ical->read('BEGIN:VCALENDAR...');
$calendar= $ical->read(Console::$in->getStream());
$calendar= $ical->read(new File('meeting.ics'));

$ical->write($calendar, Console::$out->getStream());
$ical->write($calendar, new File('meeting.ics'));
```

Events
------
Typically a calendar contains one event, though the format allows any number, including none at all.

Using first event, typical use-case:
```php
$event= $calendar->events()->first();
```

To prevent a `lang.ElementNotFoundException` when no event is present, check first:
```php
$events= $calendar->events(); 
if ($events->present()) {
  $event= $events->first();
} else {
  // Handle situation when no events are inside calendar
}
```

Process all events:
```php
foreach ($calendar->events() as $event) {
  // ...
}
```

Creation
--------
Calendar instances can be created using a fluent interface

```php
use text\ical\{
  Calendar,
  Event,
  Organizer,
  Attendee,
  Date,
  Text,
  Method,
  Role,
  PartStat
};

$calendar= Calendar::with()
  ->method(Method::REQUEST)
  ->prodid('Microsoft Exchange Server 2010')
  ->version('2.0')
  ->events([Event::with()
    ->organizer(new Organizer('The Organizer', 'MAILTO:organizer@example.com'))
    ->attendees([
      Attendee::with()
        ->role(Role::CHAIR)
        ->partstat(PartStat::NEEDS_ACTION)
        ->rsvp('TRUE')
        ->cn('The Attendee 1')
        ->value('MAILTO:attendee2@example.com')
        ->create()
      ,
      Attendee::with()
        ->role(Role::REQ_PARTICIPANT)
        ->partstat(PartStat::NEEDS_ACTION)
        ->rsvp('TRUE')
        ->cn('The Attendee 2')
        ->value('MAILTO:attendee3@example.com')
        ->create()
    ])
    ->dtstart(new Date(null, '20160524T183000Z'))
    ->dtend(new Date(null, '20160524T190000Z'))
    ->location(new Text('de-DE', 'BS 50 EG 0102'))
    ->summary(new Text('de-DE', 'Treffen'))
    ->create()
  ])
  ->create()
;
```
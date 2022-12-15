<?php namespace text\ical\unittest;

use text\ical\ICalendar;
use unittest\{Test, BeforeClass, AfterClass, Values, TestCase};
use util\Date;

class TimeZonesTest extends TestCase {
  private static $tz;

  #[BeforeClass]
  public static function useGMT() {
    self::$tz= date_default_timezone_get();
    date_default_timezone_set('GMT');
  }

  #[AfterClass]
  public static function restoreTZ() {
    date_default_timezone_set(self::$tz);
  }

  /**
   * Parses a string and returns the timezone
   *
   * @param  string $input
   * @return text.ical.TimeZone
   */
  private function parse($input) {
    $source= "BEGIN:VCALENDAR\r\n".preg_replace('/\n\s+/', "\r\n", trim($input))."\r\nEND:VCALENDAR\r\n";
    return $calendar= (new ICalendar())->read($source)->timezones()[0];
  }

  /** @return iterable */
  private function europeBerlin() {
    yield ['20180101T000000', '2018-01-01 00:00:00 Europe/Berlin'];
    yield ['20180330T192800', '2018-03-30 19:28:00 Europe/Berlin'];
  }

  /** @return iterable */
  private function americaNY() {
    yield ['20180101T000000', '2018-01-01 00:00:00 America/New_York'];
    yield ['20180401T115500', '2018-04-01 11:55:00 America/New_York'];
  }

  /** @return iterable */
  private function europeParis() {
    yield ['20070101T000000', '2007-01-01 00:00:00 Europe/Paris'];
    yield ['20061029T030000', '2006-10-29 03:00:00 Europe/Paris'];
    yield ['20070325T040000', '2007-03-25 04:00:00 Europe/Paris'];
  }

  /**
   * Handle standard to daylight savings time transitions
   * 
   * @see    https://www.nist.gov/pml/time-and-frequency-division/popular-links/daylight-saving-time-dst
   * @see    https://www.rfc-editor.org/rfc/rfc5545#section-3.3.5
   * @see    https://tc39.es/proposal-temporal/docs/ambiguity.html
   * @return iterable
   */
  private function transitions() {

    // Transition from standard to daylight savings time
    // Wall clock goes 01:59:59 EST -> 03:00:00 EDT
    yield ['20070311T015959', '2007-03-11 01:59:59 EST', 'One second before transition to daylight'];
    yield ['20070311T020000', '2007-03-11 03:00:00 EDT', 'Transition to daylight'];
    yield ['20070311T030001', '2007-03-11 03:00:01 EDT', 'One second after transition to daylight'];

    // If the local time described does not occur (when
    // changing from standard to daylight time), the DATE-TIME value is
    // interpreted using the UTC offset before the gap in local times.
    // Thus, TZID=America/New_York:20070311T023000 indicates March 11,
    // 2007 at 3:30 A.M. EDT (UTC-04:00), one hour after 1:30 A.M. EST
    // (UTC-05:00).
    yield ['20070311T023000', '2007-03-11 03:30:00 EDT', 'Local time does not occur'];

    // Transition from daylight savings to standard time
    // Wall clock goes 01:59:59 EDT -> 01:00:00 EST.
    yield ['20071104T015959', '2007-11-04 01:59:59 EDT', 'One second before transition to standard'];
    yield ['20071104T020000', '2007-11-04 02:00:00 EST', 'One hour after transition to standard'];
    yield ['20071104T020001', '2007-11-04 02:00:01 EST', 'One hour and one second after transition to standard'];

    // If, based on the definition of the referenced time zone, the local
    // time described occurs more than once (when changing from daylight
    // to standard time), the DATE-TIME value refers to the first
    // occurrence of the referenced time.  Thus, TZID=America/
    // New_York:20071104T013000 indicates November 4, 2007 at 1:30 A.M.
    // EDT (UTC-04:00).
    yield ['20071104T013000', '2007-11-04 01:30:00 EDT', 'Local time occurs more than once'];

    // This means there is no way to reference the repeated hour (01:00:00
    // EST until 02:00:00 EST) in local time; we need to use UTC instead.
    yield ['20071104T055959Z', '2007-11-04 01:59:59 EDT', 'One second before transition to standard'];
    yield ['20071104T060000Z', '2007-11-04 01:00:00 EST', 'Transition to standard'];
    yield ['20071104T060001Z', '2007-11-04 01:00:01 EST', 'One second after transition to standard'];
    yield ['20071104T070000Z', '2007-11-04 02:00:00 EST', 'One hour after transition to standard'];
  }

  #[Test, Values('europeBerlin')]
  public function west_europe_standard_time_with_rrule($input, $expected) {
    $tz= $this->parse('
      BEGIN:VTIMEZONE
      TZID:W. Europe Standard Time
      BEGIN:STANDARD
      DTSTART:16010101T030000
      TZOFFSETFROM:+0200
      TZOFFSETTO:+0100
      RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10
      END:STANDARD
      BEGIN:DAYLIGHT
      DTSTART:16010101T020000
      TZOFFSETFROM:+0100
      TZOFFSETTO:+0200
      RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3
      END:DAYLIGHT
      END:VTIMEZONE
    ');
    $this->assertEquals(new Date($expected), $tz->convert($input));
  }

  #[Test, Values('americaNY')]
  public function new_york_time_with_rrule($input, $expected) {
    $tz= $this->parse('
      BEGIN:VTIMEZONE
      TZID:America/New_York
      LAST-MODIFIED:20050809T050000Z
      TZURL:http://zones.example.com/tz/America-New_York.ics
      BEGIN:STANDARD
      DTSTART:20071104T020000
      RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
      TZOFFSETFROM:-0400
      TZOFFSETTO:-0500
      TZNAME:EST
      END:STANDARD
      BEGIN:DAYLIGHT
      DTSTART:20070311T020000
      RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
      TZOFFSETFROM:-0500
      TZOFFSETTO:-0400
      TZNAME:EDT
      END:DAYLIGHT
      END:VTIMEZONE
    ');
    $this->assertEquals(new Date($expected), $tz->convert($input));
  }

  #[Test, Values('europeParis')]
  public function europe_paris_without_rrule($input, $expected) {
    $tz= $this->parse('
      BEGIN:VTIMEZONE
      TZID:Europe/Paris
      LAST-MODIFIED:20070430T230046Z
      BEGIN:STANDARD
      DTSTART:20061029T010000
      TZOFFSETTO:+0100
      TZOFFSETFROM:+0000
      TZNAME:CET
      END:STANDARD
      BEGIN:DAYLIGHT
      DTSTART:20070325T020000
      TZOFFSETTO:+0200
      TZOFFSETFROM:+0100
      TZNAME:CEST
      END:DAYLIGHT
      END:VTIMEZONE
    ');
    $this->assertEquals(new Date($expected), $tz->convert($input));
  }

  #[Test, Values('transitions')]
  public function local_date_transitions($input, $expected) {
    $tz= $this->parse('
      BEGIN:VTIMEZONE
      TZID:America/New_York
      LAST-MODIFIED:20050809T050000Z
      TZURL:http://zones.example.com/tz/America-New_York.ics
      BEGIN:STANDARD
      DTSTART:20071104T020000
      RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
      TZOFFSETFROM:-0400
      TZOFFSETTO:-0500
      TZNAME:EST
      END:STANDARD
      BEGIN:DAYLIGHT
      DTSTART:20070311T020000
      RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
      TZOFFSETFROM:-0500
      TZOFFSETTO:-0400
      TZNAME:EDT
      END:DAYLIGHT
      END:VTIMEZONE
    ');
    $this->assertEquals(new Date($expected), $tz->convert($input));
  }
}
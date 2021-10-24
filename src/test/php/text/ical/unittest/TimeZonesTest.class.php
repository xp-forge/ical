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
    yield ['20180101T000000', '2018-01-01 00:00:00 Europe/Berlin', 'Standard'];
    yield ['20180330T192800', '2018-03-30 19:28:00 Europe/Berlin', 'Daylight'];
    yield ['20180325T015959', '2018-03-25 01:59:59 Europe/Berlin', 'One second before transition to daylight'];
    yield ['20180325T020000', '2018-03-25 03:00:00 Europe/Berlin', 'Transition to daylight'];
    yield ['20180325T020001', '2018-03-25 03:00:01 Europe/Berlin', 'One second after transition to daylight'];
    yield ['20180325T030001', '2018-03-25 03:00:01 Europe/Berlin', 'One second after transition to daylight'];
    yield ['20181028T025959', '2018-10-28 02:59:59 Europe/Berlin', 'One second before transition to standard'];
    yield ['20181028T030000', '2018-10-28 03:00:00 Europe/Berlin', 'Transition to standard'];
    yield ['20181028T030001', '2018-10-28 03:00:01 Europe/Berlin', 'One second after transition to standard'];
  }

  /** @return iterable */
  private function americaNY() {
    yield ['20180101T000000', '2018-01-01 00:00:00 America/New_York', 'Standard'];
    yield ['20180401T115500', '2018-04-01 11:55:00 America/New_York', 'Daylight'];
    yield ['20180311T015959', '2018-03-11 01:59:59 America/New_York', 'One second before transition to daylight'];
    yield ['20180311T020000', '2018-03-11 03:00:00 America/New_York', 'Transition to daylight'];
    yield ['20180311T020001', '2018-03-11 03:00:01 America/New_York', 'One second after transition to daylight'];
    yield ['20180311T030001', '2018-03-11 03:00:01 America/New_York', 'One second after transition to daylight'];
    yield ['20181104T025959', '2018-11-04 02:59:59 America/New_York', 'One second before transition to standard'];
    yield ['20181104T030000', '2018-11-04 03:00:00 America/New_York', 'Transition to standard'];
    yield ['20181104T030001', '2018-11-04 03:00:01 America/New_York', 'One second after transition to standard'];
  }

  /** @return iterable */
  private function europeParis() {
    yield ['20070101T000000', '2007-01-01 00:00:00 Europe/Paris', 'Standard'];
    yield ['20061029T020000', '2006-10-29 02:00:00 Europe/Paris', 'Standard'];
    yield ['20070325T020000', '2007-03-25 02:00:00 Europe/Paris', 'Daylight'];
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
}
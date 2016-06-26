<?php namespace text\ical\unittest;

use text\ical\ICalendar;
use lang\FormatException;
use lang\ElementNotFoundException;
use io\streams\MemoryOutputStream;

class ICalendarTest extends \unittest\TestCase {

  /** @return php.Generator */
  private function fixtures() {
    foreach (Fixtures::values() as $fixture) {
      yield [$fixture];
    }
  }

  #[@test]
  public function can_create() {
    new ICalendar();
  }

  #[@test, @values('fixtures')]
  public function read($fixture) {
    $this->assertEquals($fixture->object(), (new ICalendar())->read($fixture->string()));
  }

  #[@test, @values('fixtures')]
  public function write($fixture) {
    $out= new MemoryOutputStream();
    (new ICalendar())->write($fixture->object(), $out);

    $this->assertEquals($fixture->string(), trim($out->getBytes()));
  }

  #[@test, @expect(FormatException::class), @values([
  #  "BEGIN:VCALENDAR",
  #  "BEGIN:VCALENDAR\nBEGIN:VEVENT\nEND:VCALENDAR",
  #  "BEGIN:VCALENDAR\nBEGIN:VEVENT\nEND:VEVENT"
  #])]
  public function unclosed_object($input) {
    (new ICalendar())->read($input);
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Unknown object type "event" at root level'
  #)]
  public function root_object_must_be_calendar() {
    (new ICalendar())->read("BEGIN:VEVENT");
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Unknown object type "calendar" inside "calendar"'
  #)]
  public function cannot_nest_calendars() {
    (new ICalendar())->read("BEGIN:VCALENDAR\r\nBEGIN:VCALENDAR");
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Unknown object type "unknown" inside "calendar"'
  #)]
  public function unknown_object_inside_calendar() {
    (new ICalendar())->read("BEGIN:VCALENDAR\r\nBEGIN:UNKNOWN");
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Unknown object type "unknown" inside "event"'
  #)]
  public function unknown_object_inside_calendar_event() {
    (new ICalendar())->read("BEGIN:VCALENDAR\r\nBEGIN:VEVENT\r\nBEGIN:UNKNOWN");
  }

  #[@test, @expect(
  #  class= FormatException::class,
  #  withMessage= 'Illegal nesting of "unknown" inside "calendar"'
  #)]
  public function illegal_nesting() {
    (new ICalendar())->read("BEGIN:VCALENDAR\r\nEND:UNKNOWN");
  }

  #[@test]
  public function no_events_present() {
    $calendar= (new ICalendar())->read("BEGIN:VCALENDAR\r\nEND:VCALENDAR");
    $this->assertFalse($calendar->events()->present());
  }

  #[@test, @expect(ElementNotFoundException::class)]
  public function no_events_first() {
    $calendar= (new ICalendar())->read("BEGIN:VCALENDAR\r\nEND:VCALENDAR");
    $this->assertFalse($calendar->events()->first());
  }

  #[@test]
  public function all_events_present() {
    $calendar= (new ICalendar())->read(
      "BEGIN:VCALENDAR\r\n".
      "BEGIN:VEVENT\r\n".
      "SUMMARY;LANGUAGE=de-DE:Test\r\n".
      "END:VEVENT\r\n".
      "END:VCALENDAR"
    );
    $this->assertTrue($calendar->events()->present());
  }

  #[@test]
  public function iterate_events() {
    $calendar= (new ICalendar())->read(
      "BEGIN:VCALENDAR\r\n".
      "BEGIN:VEVENT\r\n".
      "SUMMARY;LANGUAGE=de-DE:Test\r\n".
      "END:VEVENT\r\n".
      "END:VCALENDAR"
    );
    $this->assertEquals('Test', iterator_to_array($calendar->events())[0]->summary()->value());
  }

  #[@test, @values([' ', "\t"])]
  public function continued_line($continuation) {
    $calendar= (new ICalendar())->read(
      "BEGIN:VCALENDAR\r\n".
      "BEGIN:VEVENT\r\n".
      "SUMMARY;LANGUAGE=de-DE:\r\n".$continuation."Test\r\n".
      "END:VEVENT\r\n".
      "END:VCALENDAR"
    );
    $this->assertEquals('Test', $calendar->events()->first()->summary()->value());
  }

  #[@test, @values(['\n', '\N'])]
  public function linefeeds_in_data($summary) {
    $calendar= (new ICalendar())->read(
      "BEGIN:VCALENDAR\r\n".
      "BEGIN:VEVENT\r\n".
      "SUMMARY;LANGUAGE=de-DE:".$summary."\r\n".
      "END:VEVENT\r\n".
      "END:VCALENDAR"
    );
    $this->assertEquals("\n", $calendar->events()->first()->summary()->value());
  }
}
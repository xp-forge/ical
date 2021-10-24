<?php namespace text\ical;

use lang\IllegalStateException;
use util\Date;
use util\Objects;

class Calendar implements IObject {
  use Properties;

  private $method, $prodid, $version, $events, $timezone;

  /**
   * Creates a new calendar instance
   * 
   * @param  string $method
   * @param  string $prodid
   * @param  string $version
   * @param  text.ical.Event[] $events
   * @param  text.ical.ITimeZone[] $timezones
   * @param  [:string] $properties
   */
  public function __construct($method, $prodid, $version, $events, $timezones, $properties= []) {
    $this->method= $method;
    $this->prodid= $prodid;
    $this->version= $version;
    $this->events= $events;
    $this->timezones= $timezones;
    $this->properties= $properties;
  }

  /** @return string */
  public function method() { return $this->method; }

  /** @return string */
  public function prodid() { return $this->prodid; }

  /** @return string */
  public function version() { return $this->version; }

  /** @return text.ical.ITimeZone[] */
  public function timezones() { return $this->timezones; }

  /** @return text.ical.Events */
  public function events() { return new Events(...(array)$this->events); }

  /** @return object */
  public static function with() {
    return new class() {
      private $method, $prodid, $version, $events, $timezones, $properties= [];

      public function method($value) { $this->method= $value; return $this; }

      public function prodid($value) { $this->prodid= $value; return $this; }

      public function version($value) { $this->version= $value; return $this; }

      public function timezones($value) { $this->timezones= $value; return $this; }

      public function events($value) { $this->events= $value; return $this; }

      public function properties($value) { $this->properties= $value; return $this; }

      public function create() {
        return new Calendar($this->method, $this->prodid, $this->version, $this->events, $this->timezones, $this->properties);
      }
    };
  }

  /**
   * Converts a calendar date value to a date instance
   *
   * @param  text.ical.IDate $date
   * @return util.Date
   * @throws lang.IllegalStateException if the date's timezone is not defined
   */
  public function date(IDate $date) {
    if (null === ($tzid= $date->tzid())) return new Date($date->value());

    foreach ($this->timezones as $timezone) {
      if ($tzid === $timezone->tzid()) return $timezone->convert($date->value());
    }

    throw new IllegalStateException('No timezone definition in calendar for "'.$tzid.'"');
  }

  /**
   * Write this object
   *
   * @param  text.ical.Output $out
   * @param  string $name
   * @return void
   */
  public function write($out, $name) {
    $out->object('vcalendar', $this->merge([
      'method'   => $this->method,
      'prodid'   => $this->prodid,
      'version'  => $this->version,
      'event'    => $this->events,
      'timezone' => $this->timezones
    ]));
  }

  /** @return string */
  public function hashCode() { return Objects::hashOf((array)$this); }

  /** @return string */
  public function toString() { return nameof($this).'@'.Objects::stringOf(get_object_vars($this)); }

  /**
   * Compare
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? Objects::compare((array)$this, (array)$value) : 1;
  }
}
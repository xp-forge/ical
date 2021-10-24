<?php namespace text\ical;

use util\Objects;

class TimeZone implements IObject {
  private $tzid, $standard, $daylight;

  /**
   * Constructor
   *
   * @param string $tzid
   * @param text.ical.TimeZoneInfo $standard
   * @param text.ical.TimeZoneInfo $daylight
   */
  public function __construct($tzid, $standard, $daylight) {
    $this->tzid= $tzid;
    $this->standard= $standard;
    $this->daylight= $daylight;
  }

  /** @return string */
  public function tzid() { return $this->tzid; }

  /** @return text.ical.TimeZoneInfo */
  public function standard() { return $this->standard; }

  /** @return text.ical.TimeZoneInfo */
  public function daylight() { return $this->daylight; }

  /** @return object */
  public static function with() {
    return new class() {
      private $tzid, $standard, $daylight, $properties= [];

      public function tzid($value) { $this->tzid= $value; return $this; }

      public function standard($value) { $this->standard= $value; return $this; }

      public function daylight($value) { $this->daylight= $value; return $this; }

      public function create() {
        return new TimeZone($this->tzid, $this->standard, $this->daylight);
      }
    };
  }

  /**
   * Write this object
   *
   * @param  text.ical.Output $out
   * @param  string $name
   * @return void
   */
  public function write($out, $name) {
    $out->object('vtimezone', [
      'tzid'     => $this->tzid,
      'standard' => $this->standard,
      'daylight' => $this->daylight
    ]);
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
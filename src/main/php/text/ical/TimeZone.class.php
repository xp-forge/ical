<?php namespace text\ical;

use util\{Date, Objects};

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
   * Converts a date
   *
   * @param  string $input `YYYYMMDD"T"HHMMSS`
   * @return util.Date
   */
  public function convert($input) {
    $date= sscanf($input, '%4d%2d%2dT%2d%2d%d');

    $rel= gmmktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]);
    $daylight= $this->daylight->start($date[0]);
    $standard= $this->standard->start($date[0]);

    if ($rel >= $standard + $this->standard->adjust() || $rel < $daylight + $this->daylight->adjust()) {
      return new Date(gmdate('Y-m-d H:i:s'.$this->standard->tzoffsetto(), $rel));
    } else {
      return new Date(gmdate('Y-m-d H:i:s'.$this->daylight->tzoffsetto(), $rel));
    }
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
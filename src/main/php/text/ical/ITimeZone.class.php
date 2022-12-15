<?php namespace text\ical;

use DateTimeZone, Throwable;
use lang\IllegalArgumentException;
use util\{Date, Objects};

class ITimeZone implements IObject {
  private $tzid, $standard, $daylight;

  /**
   * Constructor
   *
   * @param string $tzid
   * @param text.ical.TimeZoneInfo $standard
   * @param ?text.ical.TimeZoneInfo $daylight
   */
  public function __construct($tzid, TimeZoneInfo $standard, TimeZoneInfo $daylight= null) {
    $this->tzid= $tzid;
    $this->standard= $standard;
    $this->daylight= $daylight;
  }

  /**
   * Creates an instance from a given global timezone identifier
   *
   * @param  string $id
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public static function named($id) {
    try {
      $z= new DateTimeZone($id);
    } catch (Throwable $e) {
      throw new IllegalArgumentException('No global timezone with identifier "'.$id.'"', $e);
    }

    // Fetch transitions for current year
    $y= idate('Y');
    $s= gmmktime(0, 0, 0, 1, 1, $y);
    $t= $z->getTransitions($s, gmmktime(0, 0, 0, 1, 1, $y + 1));

    // Without DST: [2022-01-01 => UTC]
    // With DST   : [2022-01-01 => CET, 2022-03-27 => CEST, 2022-10-30 => CET]
    if (3 === sizeof($t)) {
      $daylight= TimeZoneInfo::transition($t[1]['ts'], $t[0]['offset'], $t[1]['offset']);
      $standard= TimeZoneInfo::transition($t[2]['ts'], $t[1]['offset'], $t[2]['offset']);
    } else {
      $daylight= null;
      $standard= TimeZoneInfo::transition($s, $t[0]['offset'], $t[0]['offset']);
    }

    return new self($id, $standard, $daylight);
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
        return new ITimeZone($this->tzid, $this->standard, $this->daylight);
      }
    };
  }

  /**
   * Converts a date
   *
   * @param  string $input `YYYYMMDD"T"HHMMSS[Z]`
   * @return util.Date
   */
  public function convert($input) {
    $date= sscanf($input, '%4d%2d%2dT%2d%2d%d%c');
    $rel= gmmktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]);
    $utc= 'Z' === $date[6];

    // If this timezone has DST, handle it
    if (null === $this->daylight) {
      $t= $this->standard;
    } else {
      $daylight= $this->daylight->start($date[0]);
      $standard= $this->standard->start($date[0]);
      $t= $rel >= $daylight + $this->daylight->adjust() && $rel < $standard
        ? $this->daylight
        : $this->standard
      ;
    }

    return new Date(gmdate('Y-m-d H:i:s'.$t->tzoffsetto(), $utc ? $rel + $t->offset() : $rel));
  }

  /**
   * Write this object
   *
   * @param  text.ical.Output $out
   * @param  string $name
   * @return void
   */
  public function write($out, $name) {
    $out->object('vtimezone', null === $this->daylight
      ? ['tzid' => $this->tzid, 'standard' => $this->standard]
      : ['tzid' => $this->tzid, 'standard' => $this->standard, 'daylight' => $this->daylight]
    );
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
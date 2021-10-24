<?php namespace text\ical;

use util\Objects;

class TimeZoneInfo implements IObject {
  private $dtstart, $tzoffsetfrom, $tzoffsetto, $rrule;

  /**
   * Constructor
   *
   * @param string $dtstart
   * @param string $tzoffsetfrom
   * @param string $tzoffsetto
   * @param string $rrule
   */
  public function __construct($dtstart, $tzoffsetfrom, $tzoffsetto, $rrule) {
    $this->dtstart= $dtstart;
    $this->tzoffsetfrom= $tzoffsetfrom;
    $this->tzoffsetto= $tzoffsetto;
    $this->rrule= $rrule;
  }

  /** @return string */
  public function dtstart() { return $this->dtstart; }

  /** @return string */
  public function tzoffsetfrom() { return $this->tzoffsetfrom; }

  /** @return string */
  public function tzoffsetto() { return $this->tzoffsetto; }

  /** @return string */
  public function rrule() { return $this->rrule; }

  /** @return object */
  public static function with() {
    return new class() {
      private $dtstart, $tzoffsetfrom, $tzoffsetto, $rrule;

      public function dtstart($value) { $this->dtstart= $value; return $this; }

      public function tzoffsetfrom($value) { $this->tzoffsetfrom= $value; return $this; }

      public function tzoffsetto($value) { $this->tzoffsetto= $value; return $this; }

      public function rrule($value) { $this->rrule= $value; return $this; }

      public function create() {
        return new TimeZoneInfo($this->dtstart, $this->tzoffsetfrom, $this->tzoffsetto, $this->rrule);
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
    $out->object($name, [
      'dtstart'      => $this->dtstart,
      'tzoffsetfrom' => $this->tzoffsetfrom,
      'tzoffsetto'   => $this->tzoffsetto,
      'rrule'        => $this->rrule
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
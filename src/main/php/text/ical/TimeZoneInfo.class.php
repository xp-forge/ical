<?php namespace text\ical;

use lang\IllegalStateException;
use util\Objects;

class TimeZoneInfo implements IObject {
  private $dtstart, $tzoffsetfrom, $tzoffsetto, $rrule;

  /**
   * Constructor
   *
   * @param string $dtstart
   * @param string $tzoffsetfrom
   * @param string $tzoffsetto
   * @param ?string $rrule
   */
  public function __construct($dtstart, $tzoffsetfrom, $tzoffsetto, $rrule= null) {
    $this->dtstart= $dtstart;
    $this->tzoffsetfrom= $tzoffsetfrom;
    $this->tzoffsetto= $tzoffsetto;
    $this->rrule= $rrule;
  }

  /**
   * Creates an instance from a time transition
   * 
   * @param  int $ts Timestamp for the transition
   * @param  int $from UTC offset from
   * @param  int $to UTC offset to
   * @return self
   */
  public static function transition($ts, $from, $to): self {
    return new self(
      gmdate('Ymd\THis', $ts + $from - $to),
      sprintf('%s%02d:%02d', $from < 0 ? '-' : '+', abs($from / 3600), abs($from % 3600) / 60),
      sprintf('%s%02d:%02d', $to < 0 ? '-' : '+', abs($to / 3600), abs($to % 3600) / 60)
    );
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

  /** @return int */
  public function offset() {
    sscanf($this->tzoffsetto, "%c%2d:%d", $sign, $h, $m);
    return ('-' === $sign ? -1 : 1) * ($h * 3600 + $m * 60);
  }

  /** @return int */
  public function adjust() {
    sscanf($this->tzoffsetfrom, "%c%2d:%d", $sign, $h, $m);
    $from= ('-' === $sign ? -1 : 1) * ($h * 3600 + $m * 60);
    sscanf($this->tzoffsetto, "%c%2d:%d", $sign, $h, $m);
    $to= ('-' === $sign ? -1 : 1) * ($h * 3600 + $m * 60);
    return $to - $from;
  }

  /**
   * Returns start of this time for a given year in GMT
   *
   * @param  int $year
   * @return int
   */
  public function start($year) {
    static $days= ['MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6, 'SU' => 0];

    $start= sscanf($this->dtstart, '%4d%2d%2dT%2d%2d%d');
    if (null === $this->rrule) {
      return gmmktime($start[3], $start[4], $start[5], $start[1], $start[2], $year);
    } else {

      // RRULE: https://tools.ietf.org/html/rfc5545#section-3.3.10
      $r= [];
      foreach (explode(';', $this->rrule) as $attributes) {
        sscanf($attributes, "%[^=]=%[^\r]", $key, $value);
        $r[$key]= $value;
      }

      if ('YEARLY' !== $r['FREQ']) {
        throw new IllegalStateException('Unexpected frequency '.$r['FREQ']);
      }

      // -1SU = "Last Sunday in month"
      // 1SU  = "First Sunday in month"
      // 2SU  = "Second Sunday in month"
      if ('-' === $r['BYDAY'][0]) {
        $month= (int)$r['BYMONTH'] + 1;
        $by= $days[substr($r['BYDAY'], 2)];
        $last= idate('w', gmmktime(0, 0, 0, $month, -1, $year));
        $day= $by - $last - 1;
      } else {
        $month= (int)$r['BYMONTH'];
        $by= $days[substr($r['BYDAY'], 1)];
        $first= idate('w', gmmktime(0, 0, 0, $month, 0, $year));
        $day= $by + $first + 1 + 7 * ($r['BYDAY'][0] - 1);
      }

      return gmmktime($start[3], $start[4], $start[5], $month, $day, $year);
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
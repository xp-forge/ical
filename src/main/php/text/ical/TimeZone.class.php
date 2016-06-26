<?php namespace text\ical;

use lang\partial\Value;
use lang\partial\Builder;

class TimeZone implements Object {
  use TimeZone\is\Value;
  use TimeZone\with\Builder;

  /** @type string */
  private $tzid;

  /** @type text.ical.TimeZoneInfo */
  private $standard;

  /** @type text.ical.TimeZoneInfo */
  private $daylight;

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
}
<?php namespace text\ical;

use util\Objects;

class IDate implements IObject {
  private $tzid, $value;

  /**
   * Constructor
   *
   * @param string $tzid
   * @param string $value
   */
  public function __construct($tzid, $value) {
    $this->tzid= $tzid;
    $this->value= $value;
  }

  /** @return string */
  public function tzid() { return $this->tzid; }

  /** @return string */
  public function value() { return $this->value; }

  /** @return object */
  public static function with() {
    return new class() {
      private $tzid, $value;

      public function tzid($value) { $this->tzid= $value; return $this; }

      public function value($value) { $this->value= $value; return $this; }

      public function create() {
        return new Date($this->tzid, $this->value);
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
    $out->pair($name, ['tzid' => $this->tzid], $this->value);
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
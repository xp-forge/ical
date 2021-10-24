<?php namespace text\ical;

use util\Objects;

class Organizer implements IObject {
  private $cn, $value;

  /**
   * Constructor
   *
   * @param string $cn
   * @param string $value
   */
  public function __construct($cn, $value) {
    $this->cn= $cn;
    $this->value= $value;
  }

  /** @return string */
  public function cn() { return $this->cn; }

  /** @return string */
  public function value() { return $this->value; }

  /** @return object */
  public static function with() {
    return new class() {
      private $cn, $value;

      public function cn($value) { $this->cn= $value; return $this; }

      public function value($value) { $this->value= $value; return $this; }

      public function create() {
        return new Organizer($this->cn, $this->value);
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
    $out->pair('organizer', ['cn' => $this->cn], $this->value);
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
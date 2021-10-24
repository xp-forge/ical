<?php namespace text\ical;

use util\Objects;

class Trigger implements IObject {
  private $related, $value;

  /**
   * Constructor
   *
   * @param string $related
   * @param string $value
   */
  public function __construct($related, $value) {
    $this->related= $related;
    $this->value= $value;
  }

  /** @return string */
  public function related() { return $this->related; }

  /** @return string */
  public function value() { return $this->value; }

  /** @return object */
  public static function with() {
    return new class() {
      private $related, $value;

      public function related($value) { $this->related= $value; return $this; }

      public function value($value) { $this->value= $value; return $this; }

      public function create() {
        return new Trigger($this->related, $this->value);
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
    $out->pair($name, ['related' => $this->related], $this->value);
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
<?php namespace text\ical;

use util\Objects;

class Attendee implements IObject {
  private $role, $partstat, $rsvp, $type, $cn, $value;

  /**
   * Constructor
   *
   * @param string $role 
   * @param string $partstat 
   * @param string $rsvp 
   * @param string $type 
   * @param string $cn 
   * @param string $value 
   */
  public function __construct($role, $partstat, $rsvp, $type, $cn, $value) {
    $this->role= $role;
    $this->partstat= $partstat;
    $this->rsvp= $rsvp;
    $this->type= $type;
    $this->cn= $cn;
    $this->value= $value;
  }

  /** @return string */
  public function role() { return $this->role; }

  /** @return string */
  public function partstat() { return $this->partstat; }

  /** @return string */
  public function rsvp() { return $this->rsvp; }

  /** @return string */
  public function type() { return $this->type; }

  /** @return string */
  public function cn() { return $this->cn; }

  /** @return string */
  public function value() { return $this->value; }

  /** @return object */
  public static function with() {
    return new class() {
      private $role, $partstat, $rsvp, $type, $cn, $value;

      public function role($value) { $this->role= $value; return $this; }

      public function partstat($value) { $this->partstat= $value; return $this; }

      public function rsvp($value) { $this->rsvp= $value; return $this; }

      public function type($value) { $this->type= $value; return $this; }

      public function cn($value) { $this->cn= $value; return $this; }

      public function value($value) { $this->value= $value; return $this; }

      public function create() {
        return new Attendee($this->role, $this->partstat, $this->rsvp, $this->type, $this->cn, $this->value);
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
    $out->pair(
      'attendee',
      ['role' => $this->role, 'partstat' => $this->partstat, 'rsvp' => $this->rsvp, 'type' => $this->type, 'cn' => $this->cn],
      $this->value
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
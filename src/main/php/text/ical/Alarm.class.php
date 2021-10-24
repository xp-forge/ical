<?php namespace text\ical;

use util\Objects;

class Alarm implements IObject {
  use Properties;

  private $description, $trigger, $action;

  /**
   * Constructor
   *
   * @param string $description
   * @param text.ical.Trigger $trigger
   * @param string $action
   * @param  [:string] $properties
   */
  public function __construct($description, $trigger, $action, $properties= []) {
    $this->description= $description;
    $this->trigger= $trigger;
    $this->action= $action;
    $this->properties= $properties;
  }

  /** @return string */
  public function description() { return $this->description; }

  /** @return text.ical.Trigger */
  public function trigger() { return $this->trigger; }

  /** @return string */
  public function action() { return $this->action; }

  /** @return object */
  public static function with() {
    return new class() {
      private $description, $trigger, $action, $properties= [];

      public function description($value) { $this->description= $value; return $this; }

      public function trigger($value) { $this->trigger= $value; return $this; }

      public function action($value) { $this->action= $value; return $this; }

      public function properties($value) { $this->properties= $value; return $this; }

      public function create() {
        return new Alarm($this->description, $this->trigger, $this->action, $this->properties);
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
    $out->object('valarm', $this->merge([
      'description' => $this->description,
      'trigger'     => $this->trigger,
      'action'      => $this->action
    ]));
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
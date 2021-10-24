<?php namespace text\ical;

trait Properties {
  private $properties= [];

  /** @return [:string] */
  public function properties() { return $this->properties; }

  /**
   * Returns a named property
   *
   * @param  string $name
   * @param  var $default Returned if property does not exist
   * @return var
   */
  public function property($name, $default= null) {
    $lookup= strtolower($name);
    return $this->properties[$lookup] ?? $default;
  }

  /**
   * Merges object and properties
   *
   * @param  [:var] $object
   * @return [:var]
   */
  private function merge($object) {
    return array_merge($object, (array)$this->properties);
  }
}
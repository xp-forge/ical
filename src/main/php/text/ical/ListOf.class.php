<?php namespace text\ical;

use Traversable;
use lang\ElementNotFoundException;
use util\Objects;

/**
 * The `ListOf` trait creates a list of elements which can be queried by
 * offset, also creating `equals()` and `toString()` in a sensible manner.
 */
trait ListOf {
  private $backing;

  /**
   * Constructor
   *
   * @param  var... $elements
   */
  public function __construct(... $elements) {
    $this->backing= $elements;
  }

  /**
   * Returns whether elements are present
   *
   * @return bool
   */
  public function present() {
    return !empty($this->backing);
  }

  /**
   * Returns how many elements exist
   *
   * @return bool
   */
  public function size() {
    return sizeof($this->backing);
  }

  /**
   * Returns the element for a given name.
   *
   * @param  int $offset
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function at($offset) {
    if (isset($this->backing[$offset])) {
      return $this->backing[$offset];
    }
    throw new ElementNotFoundException('No element at offset '.$offset);
  }

  /**
   * Returns the first element.
   *
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function first() {
    if (empty($this->backing)) {
      throw new ElementNotFoundException('No elements');
    }
    return $this->backing[0];
  }

  /**
   * Overloads iteration
   *
   * @return  php.Iterator
   */
  public function getIterator(): Traversable {
    foreach ($this->backing as $element) {
      yield $element;
    }
  }

  /**
   * Compares this list to a given value
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? Objects::compare($this->backing, $value->backing) : 1;
  }

  /** @return string */
  public function toString() {
    return nameof($this).'@'.Objects::stringOf($this->backing);
  }

  /** @return string */
  public function hashCode() {
    return '['.Objects::hashOf($this->backing);
  }
}
<?php namespace text\ical;

use io\streams\TextWriter;

/**
 * ICAL output
 *
 * @test  xp://text.ical.unittest.OutputTest
 */
class Output {
  const WRAP = 72;

  private $writer;

  /**
   * Create a new output instance
   *
   * @param  io.streams.TextWriter $writer
   */
  public function __construct(TextWriter $writer) {
    $this->writer= $writer->withNewLine("\r\n");
  }

  /**
   * Begin an object
   *
   * @param  string $id
   * @return void
   */
  public function begin($id) {
    $this->writer->writeLine('BEGIN:'.strtoupper($id));
  }

  /**
   * Ends an object
   *
   * @param  string $id
   * @return void
   */
  public function end($id) {
    $this->writer->writeLine('END:'.strtoupper($id));
  }

  /**
   * Writes a pair
   *
   * @param  string $name
   * @param  [:string] $attributes
   * @param  var $value
   * @return void
   */
  public function pair($name, $attributes, $value) {
    if (is_array($value)) {
      foreach ($value as $element) {
        $this->pair($name, $attributes, $element);
      }
    } else if ($value instanceof IObject) {
      $value->write($this, $name);
    } else if (null !== $value) {
      $key= strtoupper($name);
      foreach ($attributes as $name => $attribute) {
        if (null === $attribute) {
          continue;
        } else if (strcspn($attribute, '=;:') < strlen($attribute)) {
          $pair= strtoupper($name).'="'.$attribute.'"';
        } else {
          $pair= strtoupper($name).'='.$attribute;
        }

        if (strlen($key) + strlen($pair) > self::WRAP) {
          $this->writer->writeLine($key.';');
          $key= ' '.$pair;
        } else {
          $key.= ';'.$pair;
        }
      }

      $this->writer->writeLine(wordwrap($key.':'.$value, self::WRAP, "\r\n  "));
    }
  }

  /**
   * Writes a complete object
   *
   * @param  string $id
   * @param  [:var] $members
   * @return void
   */
  public function object($id, $members) {
    $this->begin($id);
    foreach ($members as $name => $value) {
      $this->pair($name, [], $value);
    }
    $this->end($id);
  }
}
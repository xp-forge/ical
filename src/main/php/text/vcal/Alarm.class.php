<?php namespace text\vcal;

use lang\partial\Value;
use lang\partial\Builder;

class Alarm implements \lang\Value {
  use Alarm\is\Value;
  use Alarm\with\Builder;

  private $description, $trigger, $action;

  /**
   * Write this object
   *
   * @param  text.vcal.VCalOutput $out
   * @param  string $name
   * @return void
   */
  public function write($out, $name) {
    $out->object('valarm', [
      'description' => $this->description,
      'trigger'    => $this->trigger,
      'action'     => $this->action
    ]);
  }
}
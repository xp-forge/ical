<?php namespace text\ical;

use IteratorAggregate;
use lang\Value;

class Attendees implements Value, IteratorAggregate {
  use ListOf;
}
<?php namespace text\ical;

use IteratorAggregate;
use lang\Value;

class Events implements Value, IteratorAggregate {
  use ListOf;
}
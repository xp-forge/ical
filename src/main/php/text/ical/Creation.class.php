<?php namespace text\ical;

use lang\FormatException;
use lang\Reflection;

class Creation {
  const ROOT = '';
  const CHECK = true;

  private static $definitions= [null, null, [
    'calendar' => [Calendar::class, ['events' => 'event'], [
      'event'      => [Event::class, ['attendees' => 'attendee'], [
        'organizer'   => [Organizer::class],
        'attendee'    => [Attendee::class],
        'summary'     => [Text::class],
        'description' => [Text::class],
        'comment'     => [Text::class],
        'location'    => [Text::class],
        'dtstart'     => [Date::class],
        'dtstamp'     => [Date::class],
        'dtend'       => [Date::class],
        'alarm'       => [Alarm::class, null, [
          'trigger'     => [Trigger::class]
        ]]
      ]],
      'timezone'   => [TimeZone::class, null, [
        'standard'    => [TimeZoneInfo::class],
        'daylight'    => [TimeZoneInfo::class],
      ]],
    ]]
  ]];


  private $definition, $type, $parent, $create;
  private $members= [], $access= [];

  /**
   * Create new instance creation 
   *
   * @param  var $definitions Selection of static definitions
   * @param  string $type
   * @param  self $parent
   */
  public function __construct($definitions, $type, $parent) {
    if (null === $definitions) {
      $this->create= function() { return $this->members['properties']['value']; };
    } else if (null === $definitions[0]) {
      $this->create= function() { return null; };
    } else {
      $constructor= Reflection::of($definitions[0])->constructor();
      foreach ($constructor->parameters() as $parameter) {
        $name= $parameter->name();
        if (isset($definitions[1][$name])) {
          $this->access[$definitions[1][$name]]= $name;
        } else {
          $this->access[$name]= false;
        }
        $this->members[$name]= $parameter->optional() ? $parameter->default() : null;
      }

      $this->create= function() use($constructor) {
        return $constructor->newInstance(array_values($this->members));
      };
    }

    $this->definition= $definitions;
    $this->type= $type;
    $this->parent= $parent;
  }

  /**
   * Nested creation
   *
   * @param  string $type
   * @param  bool $check Whether to check object type definition exists
   * @return self
   * @throws lang.FormatException
   */
  public function of($type, $check= false) {
    $lookup= strtolower($type);
    if (isset($this->definition[2][$lookup])) {
      return new self($this->definition[2][$lookup], $lookup, $this);
    } else if (!$check) {
      return new self(null, $lookup, $this);
    }

    throw new FormatException(sprintf(
      'Unknown object type "%s" %s',
      $lookup,
      $this->parent ? 'inside "'.$this->type.'"' : 'at root level'
    ));
  }

  /**
   * Root creation
   *
   * @return self
   */
  public static function root() { return new self(self::$definitions, self::ROOT, null); }

  /**
   * Set a member 
   *
   * @param  string $member
   * @param  var $value
   * @return self
   */
  public function with($member, $value) {
    $member= strtolower($member);
    if (!isset($this->access[$member])) {
      $this->members['properties'][$member]= $value;
    } else if ($this->access[$member]) {
      $this->members[$this->access[$member]][]= $value;
    } else {
      $this->members[$member]= $value;
    }
    return $this;
  }

  /**
   * Creates a new instance
   *
   * @return var
   */
  public function create() { return ($this->create)(); }

  /**
   * Close creation
   *
   * @return self
   */
  public function close($type) {
    $lookup= strtolower($type);
    if ($lookup === $this->type) return $this->parent;

    throw new FormatException(sprintf(
      'Illegal nesting of "%s" %s',
      $lookup,
      $this->parent ? 'inside "'.$this->type.'"' : 'at root level'
    ));
  }
}
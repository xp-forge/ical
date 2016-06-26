<?php namespace text\ical;

use lang\mirrors\TypeMirror;
use lang\FormatException;

class Creation {
  private static $definitions= [null, [
    'vcalendar'   => [Calendar::class, [
      'vevent'      => [Event::class, [
        'organizer'   => [Organizer::class],
        'attendee'    => [Attendee::class],
        'summary'     => [Text::class],
        'description' => [Text::class],
        'comment'     => [Text::class],
        'location'    => [Text::class],
        'dtstart'     => [Date::class],
        'dtend'       => [Date::class],
        'valarm'      => [Alarm::class, [
          'trigger'     => [Trigger::class]
        ]]
      ]],
      'vtimezone'   => [TimeZone::class, [
        'standard'    => [TimeZoneInfo::class],
        'daylight'    => [TimeZoneInfo::class],
      ]],
    ]
  ]]];

  private $definition, $parent;
  private $members= [];

  /**
   * Create new instance creation 
   *
   * @param  var $definition Selection of static definitions
   * @param  string $parent Parent path
   */
  public function __construct($definition, $parent) {
    $this->definition= $definition;
    $this->parent= $parent;
  }

  /** @return string */
  public function parent() { return ltrim($this->parent, '/'); }

  /** @return bool */
  public function isRoot() { return null === $this->parent; }

  /**
   * Nested creation
   *
   * @param  string $type
   * @return self
   * @throws lang.FormatException
   */
  public function of($type) {
    $lookup= strtolower($type);
    if (isset($this->definition[1][$lookup])) {
      return new self($this->definition[1][$lookup], $this->parent.'/'.$lookup);
    }

    throw new FormatException(sprintf(
      'Unknown object type "%s" %s',
      $lookup,
      $this->parent ? 'inside "'.$this->parent().'"' : 'at root level'
    ));
  }

  /**
   * Root creation
   *
   * @return self
   */
  public static function root() {
    return new self(self::$definitions, null);
  }

  /**
   * Set a member 
   *
   * @param  string $member
   * @param  var $value
   * @return self
   */
  public function with($member, $value) {
    $member= strtolower($member);
    if (!array_key_exists($member, $this->members)) {
      $this->members[$member]= $value;
    } else if (is_array($this->members[$member])) {
      $this->members[$member][]= $value;
    } else {
      $this->members[$member]= [$this->members[$member], $value];
    }
    return $this;
  }

  /**
   * Creates a new instance
   *
   * @return var
   */
  public function create() {
    $args= [];
    $constructor= (new TypeMirror($this->definition[0]))->constructor();
    foreach ($constructor->parameters() as $parameter) {
      $name= $parameter->name();
      $args[]= isset($this->members[$name]) ? $this->members[$name] : null;
    }
    return $constructor->newInstance(...$args);
  }
}
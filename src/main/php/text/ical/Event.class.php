<?php namespace text\ical;

use util\Objects;

class Event implements IObject {
  use Properties;

  private $organizer, $attendees, $description, $summary, $comment, $dtstart, $dtend, $dtstamp, $uid, $class, $priority, $transp, $sequence, $status, $location, $alarm;

  /**
   * Constructor
   *
   * @param  text.ical.Organizer $organizer
   * @param  text.ical.Text $description
   * @param  text.ical.Text $summary
   * @param  text.ical.Text $comment
   * @param  text.ical.IDate $dtstart
   * @param  text.ical.IDate $dtend
   * @param  text.ical.IDate $dtstamp
   * @param  string $uid
   * @param  string $class
   * @param  string $priority
   * @param  string $transp
   * @param  string $sequence
   * @param  string $status
   * @param  text.ical.Text $location
   * @param  text.ical.Alarm $alarm
   * @param  text.ical.Attendees $attendees
   * @param  [:string] $properties
   */
  public function __construct($organizer, $attendees, $description, $summary, $comment, $dtstart, $dtend, $dtstamp, $uid, $class, $priority, $transp, $sequence, $status, $location, $alarm, $properties= []) {
    $this->organizer= $organizer;
    $this->description= $description;
    $this->summary= $summary;
    $this->comment= $comment;
    $this->dtstart= $dtstart;
    $this->dtend= $dtend;
    $this->dtstamp= $dtstamp;
    $this->uid= $uid;
    $this->class= $class;
    $this->priority= $priority;
    $this->transp= $transp;
    $this->sequence= $sequence;
    $this->status= $status;
    $this->location= $location;
    $this->alarm= $alarm;
    $this->attendees= $attendees;
    $this->properties= $properties;
  }

  /** @return text.ical.Organizer */
  public function organizer() { return $this->organizer; }

  /** @return text.ical.Text */
  public function description() { return $this->description; }

  /** @return text.ical.Text */
  public function summary() { return $this->summary; }

  /** @return text.ical.Text */
  public function comment() { return $this->comment; }

  /** @return text.ical.IDate */
  public function dtstart() { return $this->dtstart; }

  /** @return text.ical.IDate */
  public function dtend() { return $this->dtend; }

  /** @return text.ical.IDate */
  public function dtstamp() { return $this->dtstamp; }

  /** @return string */
  public function uid() { return $this->uid; }

  /** @return string */
  public function class() { return $this->class; }

  /** @return string */
  public function priority() { return $this->priority; }

  /** @return string */
  public function transp() { return $this->transp; }

  /** @return string */
  public function sequence() { return $this->sequence; }

  /** @return string */
  public function status() { return $this->status; }

  /** @return text.ical.Text */
  public function location() { return $this->location; }

  /** @return text.ical.Alarm */
  public function alarm() { return $this->alarm; }

  /** @return text.ical.Attendees */
  public function attendees() { return new Attendees(...(array)$this->attendees); }

  /** @return object */
  public static function with() {
    return new class() {
      private $organizer, $attendees, $description, $summary, $comment, $dtstart, $dtend, $dtstamp, $uid, $class, $priority, $transp, $sequence, $status, $location, $alarm, $properties= [];

      public function organizer($value) { $this->organizer= $value; return $this; }

      public function attendees($value) { $this->attendees= $value; return $this; }

      public function description($value) { $this->description= $value; return $this; }

      public function summary($value) { $this->summary= $value; return $this; }

      public function comment($value) { $this->comment= $value; return $this; }

      public function dtstart($value) { $this->dtstart= $value; return $this; }

      public function dtend($value) { $this->dtend= $value; return $this; }

      public function dtstamp($value) { $this->dtstamp= $value; return $this; }

      public function uid($value) { $this->uid= $value; return $this; }

      public function class($value) { $this->class= $value; return $this; }

      public function priority($value) { $this->priority= $value; return $this; }

      public function transp($value) { $this->transp= $value; return $this; }

      public function sequence($value) { $this->sequence= $value; return $this; }

      public function status($value) { $this->status= $value; return $this; }

      public function location($value) { $this->location= $value; return $this; }

      public function alarm($value) { $this->alarm= $value; return $this; }

      public function properties($value) { $this->properties= $value; return $this; }

      public function create() {
        return new Event(
          $this->organizer,
          $this->attendees,
          $this->description,
          $this->summary,
          $this->comment,
          $this->dtstart,
          $this->dtend,
          $this->dtstamp,
          $this->uid,
          $this->class,
          $this->priority,
          $this->transp,
          $this->sequence,
          $this->status,
          $this->location,
          $this->alarm,
          $this->properties
        );
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
    $out->object('vevent', $this->merge([
      'organizer'   => $this->organizer,
      'attendee'    => $this->attendees,
      'description' => $this->description,
      'comment'     => $this->comment,
      'summary'     => $this->summary,
      'dtstart'     => $this->dtstart,
      'dtend'       => $this->dtend,
      'dtstamp'     => $this->dtstamp,
      'uid'         => $this->uid,
      'class'       => $this->class,
      'priority'    => $this->priority,
      'transp'      => $this->transp,
      'location'    => $this->location,
      'status'      => $this->status,
      'sequence'    => $this->sequence,
      'alarm'       => $this->alarm
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
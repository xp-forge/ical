<?php namespace text\ical;

use io\streams\{TextReader, TextWriter};
use lang\FormatException;

/**
 * iCalendar format I/O
 *
 * @test  xp://text.ical.unittest.ICalendarTest
 */
class ICalendar {

  /**
   * Reads iCAL format
   *
   * @param  io.streams.InputStream|io.Channel|string $arg
   * @param  string $charset Optional, defaults to UTF-8
   * @return text.ical.Calendar
   * @throws lang.FormatException
   */
  public function read($arg, $charset= \xp::ENCODING) {
    $creation= Creation::root();
    $instance= null;

    $input= new Input(new TextReader($arg, $charset));
    while (null !== ($line= $input->contentline())) {
      $p= strcspn($line, ':;');
      $token= substr($line, 0, $p);
      if ('BEGIN' === $token) {
        $creation= $creation->of(ltrim(substr($line, $p + 1), 'vV'), Creation::CHECK);
        continue;
      } else if ('END' === $token) {
        $type= ltrim(substr($line, $p + 1), 'vV');
        $instance= $creation->create();
        $creation= $creation->close($type);
        $creation->with($type, $instance);
        continue;
      }

      $property= $creation->of($token);
      if (';' === $line[$p]) {
        do {
          $e= strcspn($line, '=', $p);
          $name= substr($line, $p + 1, $e - 1);
          $p+= $e + 1;
          if ('"' === $line[$p]) {
            $q= strcspn($line, '"', $p + 1);
            $attribute= substr($line, $p + 1, $q);
            $p+= $q + 2;
          } else {
            $q= strcspn($line, ';:', $p);
            $attribute= substr($line, $p, $q);
            $p+= $q;
          }
          $property->with($name, $attribute);
        } while (':' !== $line[$p]);
      }

      $value= strtr(substr($line, $p + 1), ['\n' => "\n", '\N' => "\n", '\,' => ',', '\;' => ';', '\\\\' => '\\']);
      $creation->with($token, $property->with('value', $value)->create());
    }

    if (null === $instance) {
      throw new FormatException('No object type at root level');
    }

    $creation->close(Creation::ROOT);
    return $instance;
  }

  /**
   * Writes iCAL format
   *
   * @param  text.ical.Calendar $calendar
   * @param  io.streams.OutputStream|io.Channel|string $arg
   * @param  string $charset Optional, defaults to UTF-8
   * @return void
   * @throws lang.FormatException
   */
  public function write($calendar, $arg, $charset= \xp::ENCODING) {
    $calendar->write(new Output(new TextWriter($arg, $charset)), null);
  }
}
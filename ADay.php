<?php declare(strict_types = 1);

/**
 * Advent of code - Day.
 *
 * @author Michal Chvila
 * @since  2024-12-11
 */
abstract class ADay
{
  /**
   * Run part one.
   *
   * @return string Result.
   */
  abstract public function partOne(): string;

  /**
   * Run part two.
   *
   * @return string Result.
   */
  abstract public function partTwo(): string;

  /**
   * Run both parts.
   *
   * @return void
   */
  public function run(): void
  {
    echo 'Part One:' . PHP_EOL;
    [$partOneResult, $partOneTime] = self::measure(fn() => $this->partOne());
    echo '  Result: ' . $partOneResult . PHP_EOL;
    echo '  Time: ' . $partOneTime . ' ms' .  PHP_EOL;
    echo PHP_EOL;

    echo 'Part Two:' . PHP_EOL;
    [$partTwoResult, $partTwoTime] = self::measure(fn() => $this->partTwo());
    echo '  Result: ' . $partTwoResult . PHP_EOL;
    echo '  Time: ' . $partTwoTime . ' ms' . PHP_EOL;
    echo PHP_EOL;
  }

  /**
   * Run callback & measure time.
   *
   * @template TResult
   *
   * @param callable(mixed...): TResult $fn
   * @param mixed[]                     ...$args
   *
   * @return array{TResult, float} [result, millis]
   */
  public static function measure(callable $fn, mixed ...$args): array
  {
    $start = microtime(true);
    $result = $fn(...$args);
    $end = microtime(true);
    return [$result, ($end - $start) * 1000];
  }

  /**
   * Read input file into a string.
   *
   * @param string $file
   *
   * @return string
   */
  public static function readFile(string $file): string
  {
    $contents = file_get_contents($file);
    if($contents === false)
    {
      throw new RuntimeException('Failed to read file: ' . $file);
    }

    return trim(str_replace(["\r\n"], ["\n"], $contents));
  }

  /**
   * Read input file as whitespace separated integer values.
   *
   * @param string $file
   *
   * @return list<int>
   */
  public static function readIntegers(string $file): array
  {
    $input = self::readFile($file);
    return array_map(static fn(string $x) => (int) $x, explode(' ', $input));
  }

  /**
   * Read input file as lines.
   *
   * @param string $file
   *
   * @return list<string>
   */
  public static function readLines(string $file): array
  {
    $input = self::readFile($file);
    return explode("\n", $input);
  }

  /**
   * Read input file as a map.
   *
   * @param string $file
   *
   * @return list<list<string>> [x => [y => cell]]
   */
  public static function readMap(string $file): array
  {
    $lines = self::readLines($file);
    $map = [];

    foreach($lines as $y => $line)
    {
      foreach(str_split($line) as $x => $cell)
      {
        $map[$x][$y] = $cell;
      }
    }

    return $map;
  }
}

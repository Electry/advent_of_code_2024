<?php declare(strict_types=1);

require __DIR__ . '/../ADay.php';

/**
 * Day 11.
 *
 * @author Michal Chvila
 * @since  2024-12-11
 */
final class Day11 extends ADay
{
  /** @var array<int, array<int, int>> [number => [iterations => resultCount]] */
  private array $cache = [];

  /** @inheritDoc */
  public function partOne(): string
  {
    $numbers = self::readIntegers(__DIR__ . '/input.txt');
    $sum = 0;

    foreach($numbers as $number)
    {
      $sum += $this->iterate($number, 25);
    }

    return (string) $sum;
  }

  /** @inheritDoc */
  public function partTwo(): string
  {
    $numbers = self::readIntegers(__DIR__ . '/input.txt');
    $sum = 0;

    foreach($numbers as $number)
    {
      $sum += $this->iterate($number, 75);
    }

    return (string) $sum;
  }

  /**
   * Iterate single number.
   *
   * @param int $number
   * @param int $iterations
   *
   * @return int
   */
  private function iterate(int $number, int $iterations): int
  {
    if(isset($this->cache[$number][$iterations]))
    {
      return $this->cache[$number][$iterations];
    }

    if($iterations === 0)
    {
      return 1;
    }

    if($number === 0)
    {
      $result = $this->iterate(1, $iterations - 1);

      $this->cache[$number][$iterations] = $result;
      return $result;
    }

    if(strlen((string) $number) % 2 !== 0)
    {
      $result = $this->iterate($number * 2024, $iterations - 1);

      $this->cache[$number][$iterations] = $result;
      return $result;
    }

    [$lhs, $rhs] = $this->splitNumber($number);

    $result = $this->iterate($lhs, $iterations - 1)
      + $this->iterate($rhs, $iterations - 1);

    $this->cache[$number][$iterations] = $result;
    return $result;
  }

  /**
   * Split number into two.
   *
   * @param int $number
   *
   * @return array{string, string} [lhs, rhs]
   */
  private function splitNumber(int $number): array
  {
    $numberString = (string) $number;
    $halfPos = strlen($numberString) / 2;

    return [
      (int) substr($numberString, 0, $halfPos),
      (int) substr($numberString, $halfPos)
    ];
  }
}

$day = new Day11();
$day->run();

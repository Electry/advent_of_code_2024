<?php declare(strict_types=1);

require __DIR__ . '/../ADay.php';

/**
 * Day 14.
 *
 * @author Michal Chvila
 * @since  2024-12-14
 */
final class Day14 extends ADay
{
  /** @inheritDoc */
  public function partOne(): string
  {
    $lines = self::readLines(__DIR__ . '/input.txt');
    $robots = $this->getRobots($lines);

    $mapSize = [101, 103];
    $robotCoords = [];

    foreach($robots as $robot)
    {
      $robotCoords[] = $this->iterateRobot($robot, $mapSize, 100);
    }

    return (string) $this->calculateSafetyFactor($robotCoords, $mapSize);
  }

  /** @inheritDoc */
  public function partTwo(): string
  {
    $lines = self::readLines(__DIR__ . '/input.txt');
    $robots = $this->getRobots($lines);

    $mapSize = [101, 103];

    for($i = 0; $i < 10403; $i++)
    {
      $robotCoords = [];

      foreach($robots as $robot)
      {
        $robotCoords[] = $this->iterateRobot($robot, $mapSize, $i);
      }

      if($this->hasChristmasTreeTrunk($robotCoords, $mapSize))
      {
        return (string) $i;
      }
    }

    return 'not found';
  }

  /**
   * Advance robot's position by X seconds/iterations.
   *
   * @param array{p: array{int, int}, v: array{int, int}} $robot
   * @param array{int, int}                               $mapSize
   * @param int                                           $iterations
   *
   * @return array{int, int}
   */
  private function iterateRobot(array $robot, array $mapSize, int $iterations): array
  {
    $pos = $robot['p'];
    $vec = $robot['v'];

    return [
      $this->iteratePosition($pos[0], $vec[0], $mapSize[0], $iterations),
      $this->iteratePosition($pos[1], $vec[1], $mapSize[1], $iterations),
    ];
  }

  /**
   * Advance position on an axis by X seconds/iterations.
   *
   * @param int $pos
   * @param int $vec
   * @param int $mapSize
   * @param int $iterations
   *
   * @return int
   */
  private function iteratePosition(int $pos, int $vec, int $mapSize, int $iterations): int
  {
    $pos += $vec * $iterations;

    if($pos < 0)
    {
      $pos += ceil(abs($pos / $mapSize)) * $mapSize;
    }

    $pos %= $mapSize;
    return $pos;
  }

  /**
   * Calculate safety factor for given robot coordinates.
   *
   * @param list<array{int, int}> $robotCoords
   * @param array{int, int}       $mapSize
   *
   * @return int
   */
  private function calculateSafetyFactor(array $robotCoords, array $mapSize): int
  {
    $quadrants = [];

    foreach($robotCoords as [$x, $y])
    {
      if($x === (int)($mapSize[0] / 2) || $y === (int)($mapSize[1] / 2))
      {
        continue;
      }

      $xQuadrant = ($x < ($mapSize[0] / 2)) ? 0 : 1;
      $yQuadrant = ($y < ($mapSize[1] / 2)) ? 0 : 1;

      $quadrants[$xQuadrant . '.' . $yQuadrant] ??= 0;
      $quadrants[$xQuadrant . '.' . $yQuadrant]++;
    }

    return array_product($quadrants);
  }

  /**
   * Get robots from input data.
   *
   * @param list<string> $lines
   *
   * @return list<array{p: array{int, int}, v: array{int, int}}>
   */
  private function getRobots(array $lines): array
  {
    $robots = [];

    foreach($lines as $line)
    {
      $matches = [];
      preg_match('/p=(-?\d+),(-?\d+) v=(-?\d+),(-?\d+)/', $line, $matches);
      $robots[] = ['p' => [(int) $matches[1], (int) $matches[2]], 'v' => [(int) $matches[3], (int) $matches[4]]];
    }

    return $robots;
  }

  /**
   * Check whether the robot coords form a Christmas tree trunk.
   *
   * @param list<array{int, int}> $robotCoords
   * @param array{int, int}       $mapSize
   *
   * @return bool
   */
  private function hasChristmasTreeTrunk(array $robotCoords, array $mapSize): bool
  {
    $columns = [];

    for($x = 0; $x < $mapSize[0]; $x++)
    {
      $columns[$x] = array_fill(0, $mapSize[1], '.');
    }

    foreach($robotCoords as [$x, $y])
    {
      $columns[$x][$y] = 'X';
    }

    $searchString = str_repeat('X', 20);

    for($x = 0; $x < $mapSize[0]; $x++)
    {
      if(str_contains(implode('', $columns[$x]), $searchString))
      {
        return true;
      }
    }

    return false;
  }
}

$day = new Day14();
$day->run();

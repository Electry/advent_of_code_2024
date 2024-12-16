<?php declare(strict_types=1);

require __DIR__ . '/../ADay.php';

/**
 * Day 15.
 *
 * @author Michal Chvila
 * @since  2024-12-15
 */
final class Day15 extends ADay
{
  /** @inheritDoc */
  public function partOne(): string
  {
    $lines = self::readLines(__DIR__ . '/input.txt');

    $map = $this->getMap($lines);
    $moves = $this->getMoves($lines);

    foreach(str_split($moves) as $move)
    {
      $this->move($map, $this->getRobotPosition($map), $move);
    }

    return (string) $this->calculateGpsCoordinateSum($map);
  }

  /** @inheritDoc */
  public function partTwo(): string
  {
    $lines = self::readLines(__DIR__ . '/input.txt');

    $map = $this->getWideMap($lines);
    $moves = $this->getMoves($lines);

    foreach(str_split($moves) as $move)
    {
      $this->move($map, $this->getRobotPosition($map), $move);
    }

    return (string) $this->calculateGpsCoordinateSum($map);
  }

  /**
   * Calculate GPS coordinate sum of all boxes.
   *
   * @param list<list<string>> $map
   *
   * @return int
   */
  private function calculateGpsCoordinateSum(array $map): int
  {
    $sum = 0;

    foreach($map as $x => $col)
    {
      foreach($col as $y => $cell)
      {
        if($cell === 'O' || $cell === '[')
        {
          $sum += $x + ($y * 100);
        }
      }
    }

    return $sum;
  }

  /**
   * Get robot's position.
   *
   * @param list<list<string>> $map
   *
   * @return array{int, int}|null
   */
  private function getRobotPosition(array $map): ?array
  {
    foreach($map as $x => $col)
    {
      foreach($col as $y => $cell)
      {
        if($cell === '@')
        {
          return [$x, $y];
        }
      }
    }

    return null;
  }

  /**
   * Move cell in given direction.
   *
   * @param list<list<string>> $map
   * @param array{int, int}    $pos
   * @param string             $direction
   *
   * @return bool
   */
  private function move(array &$map, array $pos, string $direction): bool
  {
    $originalMap = $map;
    $dirVec = match($direction)
    {
      '^' => [0, -1],
      'v' => [0, 1],
      '<' => [-1, 0],
      '>' => [1, 0],
    };

    [$x, $y] = $pos;
    $x2 = match($map[$x][$y])
    {
      '[' => $x + 1,
      ']' => $x - 1,
      default => null
    };

    if($direction !== '^' && $direction !== 'v')
    {
      $x2 = null;
    }

    [$nextX, $nextY] = [$x + $dirVec[0], $y + $dirVec[1]];
    $nextX2 = $x2 !== null ? ($x2 + $dirVec[0]) : null;

    if($map[$nextX][$nextY] === '#' || ($nextX2 !== null && $map[$nextX2][$nextY] === '#'))
    {
      return false;
    }

    if($map[$nextX][$nextY] !== '.'
      && !$this->move($map, [$nextX, $nextY], $direction))
    {
      $map = $originalMap;
      return false;
    }

    if($nextX2 !== null
      && $map[$nextX2][$nextY] !== '.'
      && !$this->move($map, [$nextX2, $nextY], $direction))
    {
      $map = $originalMap;
      return false;
    }

    $map[$nextX][$nextY] = $map[$x][$y];
    $map[$x][$y] = '.';

    if($nextX2 !== null)
    {
      $map[$nextX2][$nextY] = $map[$x2][$y];
      $map[$x2][$y] = '.';
    }

    return true;
  }

  /**
   * Get basic map from input lines.
   *
   * @param list<string> $lines
   *
   * @return list<list<string>>
   */
  private function getMap(array $lines): array
  {
    $emptyLineKey = array_search('', $lines);
    $mapLines = array_slice($lines, 0, $emptyLineKey);

    return self::parseMapFromLines($mapLines);
  }

  /**
   * Get wide map from input lines.
   *
   * @param list<string> $lines
   *
   * @return list<list<string>>
   */
  private function getWideMap(array $lines): array
  {
    foreach($lines as $key => $line)
    {
      $lines[$key] = str_replace(['#', 'O', '.', '@'], ['##', '[]', '..', '@.'], $line);
    }

    return $this->getMap($lines);
  }

  /**
   * Get moves as string from input lines.
   *
   * @param list<string> $lines
   *
   * @return string
   */
  private function getMoves(array $lines): string
  {
    $emptyLineKey = array_search('', $lines);
    $moveLines = array_slice($lines, $emptyLineKey + 1);

    return implode('', $moveLines);
  }
}

$day = new Day15();
$day->run();

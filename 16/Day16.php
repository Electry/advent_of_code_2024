<?php declare(strict_types=1);

require __DIR__ . '/../ADay.php';

/**
 * Day 16.
 *
 * @author Michal Chvila
 * @since  2024-12-16
 */
final class Day16 extends ADay
{
  /** @inheritDoc */
  public function partOne(): string
  {
    $map = self::readMap(__DIR__ . '/input.txt');

    [$startX, $starY] = $this->getPosition($map, 'S');
    [$endX, $endY] = $this->getPosition($map, 'E');

    $visited = [];
    $this->traverse($map, $startX, $starY, '>', 0, null, null, $visited);

    $bestScore = min(array_values(array_map(static fn(array $x) => $x['score'], $visited[$endX][$endY])));
    return (string) $bestScore;
  }

  /** @inheritDoc */
  public function partTwo(): string
  {
    $map = self::readMap(__DIR__ . '/input.txt');

    [$startX, $startY] = $this->getPosition($map, 'S');
    [$endX, $endY] = $this->getPosition($map, 'E');

    $visited = [];
    $this->traverse($map, $startX, $startY, '>', 0, null, null, $visited);

    $bestEndDirections = [];
    $bestScore = min(array_values(array_map(static fn(array $x) => $x['score'], $visited[$endX][$endY])));

    foreach($visited[$endX][$endY] as $direction => $v)
    {
      if($v['score'] === $bestScore)
      {
        $bestEndDirections[] = $direction;
      }
    }

    $benches = [];
    foreach($bestEndDirections as $direction)
    {
      $this->backstep($endX, $endY, $direction, $visited, $map, $benches);
    }

    return (string) (count($benches) + 1);
  }

  /**
   * Backstep.
   *
   * @param int                                                                                            $x
   * @param int                                                                                            $y
   * @param string                                                                                         $direction
   * @param array<int, array<int, array<string, array{score: int, paths: list<array{int, int, string}>}>>> $visited
   * @param list<list<string>>                                                                             $map
   * @param array<string, true>                                                                            $benches
   *
   * @return void
   */
  private function backstep(int $x, int $y, string $direction, array &$visited, array &$map, array &$benches): void
  {
    foreach($visited[$x][$y][$direction]['paths'] ?? [] as $path)
    {
      foreach($path as [$x2, $y2, $direction2])
      {
        $map[$x2][$y2] = 'O';

        $key = $x2 . '.' . $y2;
        if(isset($benches[$key]))
        {
          continue;
        }

        $benches[$key] = true;
        $this->backstep($x2, $y2, $direction2, $visited, $map, $benches);
      }
    }
  }

  /**
   * Traverse.
   *
   * @param list<list<string>>                                                                          $map
   * @param int                                                                                         $x
   * @param int                                                                                         $y
   * @param string                                                                                      $direction
   * @param int                                                                                         $score
   * @param array{int, int, string}|null                                                                $previous
   * @param array{int, int, string}|null                                                                $previous2
   * @param array<int, array<int, array<string, array{score: int, paths: list<array{int, int, int}>}>>> $visited
   *
   * @return void
   */
  private function traverse(array $map, int $x, int $y, string $direction, int $score, ?array $previous, ?array $previous2, array &$visited): void
  {
    if(!isset($map[$x][$y]))
    {
      return; // cannot go oob
    }

    if($map[$x][$y] === '#')
    {
      return; // cannot traverse walls
    }

    // retarded optimization
    foreach(['^', 'v', '>', '<'] as $d)
    {
      if(($visited[$x][$y][$d]['score'] ?? (PHP_INT_MAX - 2000)) + 2000 < $score)
      {
        return;
      }
    }

    $prevPath = [
      ...($previous !== null ? [$previous] : []),
      ...($previous2 !== null ? [$previous2] : [])
    ];

    // if better route to current position was already found
    if(($visited[$x][$y][$direction]['score'] ?? PHP_INT_MAX) <= $score)
    {
      // track new possible route
      if($visited[$x][$y][$direction]['score'] === $score && !empty($prevPath))
      {
        $visited[$x][$y][$direction]['paths'][] = $prevPath;
      }

      return;
    }

    // track route
    $visited[$x][$y][$direction] = ['score' => $score, 'paths' => !empty($prevPath) ? [$prevPath] : []];

    // try moving
    [$dirVecX, $dirVecY] = $this->getDirectionVector($direction);
    $this->traverse($map, $x + $dirVecX, $y + $dirVecY, $direction, $score + 1, [$x, $y, $direction], $previous, $visited);

    // try rotating CW & moving
    $cwDirection = $this->rotateClockwise($direction);
    [$dirVecX, $dirVecY] = $this->getDirectionVector($cwDirection);
    $this->traverse($map, $x + $dirVecX, $y + $dirVecY, $cwDirection, $score + 1001, [$x, $y, $direction], $previous, $visited);

    // try rotating CCW & moving
    $ccwDirection = $this->rotateCounterClockwise($direction);
    [$dirVecX, $dirVecY] = $this->getDirectionVector($ccwDirection);
    $this->traverse($map, $x + $dirVecX, $y + $dirVecY, $ccwDirection, $score + 1001, [$x, $y, $direction], $previous, $visited);
  }

  /**
   * Get vector for direction.
   *
   * @param string $direction
   *
   * @return array{int, int}
   */
  private function getDirectionVector(string $direction): array
  {
    return match($direction)
    {
      '^' => [0, -1],
      'v' => [0, 1],
      '<' => [-1, 0],
      '>' => [1, 0],
    };
  }

  /**
   * Get CW direction.
   *
   * @param string $direction
   *
   * @return string
   */
  private function rotateClockwise(string $direction): string
  {
    return match($direction)
    {
      '^' => '>',
      'v' => '<',
      '<' => '^',
      '>' => 'v',
    };
  }

  /**
   * Get CCW direction.
   *
   * @param string $direction
   *
   * @return string
   */
  private function rotateCounterClockwise(string $direction): string
  {
    return match($direction)
    {
      '^' => '<',
      'v' => '>',
      '<' => 'v',
      '>' => '^',
    };
  }

  /**
   * Find cell on a map.
   *
   * @param list<list<string>> $map
   * @param string             $value
   *
   * @return array{int, int}
   */
  private function getPosition(array $map, string $value): array
  {
    foreach($map as $x => $col)
    {
      foreach($col as $y => $cell)
      {
        if($cell === $value)
        {
          return [$x, $y];
        }
      }
    }

    return [-1, -1];
  }
}

$day = new Day16();
$day->run();

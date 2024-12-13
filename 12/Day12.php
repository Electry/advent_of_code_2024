<?php declare(strict_types=1);

require __DIR__ . '/../ADay.php';

/**
 * Day 12.
 *
 * @author Michal Chvila
 * @since  2024-12-12
 */
final class Day12 extends ADay
{
  /** @inheritDoc */
  public function partOne(): string
  {
    $map = self::readMap(__DIR__ . '/input.txt');

    $regions = $this->getRegions($map);
    $cost = 0;

    foreach($regions as $cell => $cellRegions)
    {
      foreach($cellRegions as $regionCoords)
      {
        $perimeter = $this->getRegionPerimeter($map, $cell, $regionCoords);
        $area = count($regionCoords);
        $cost += ($area * $perimeter);
      }
    }

    return (string) $cost;
  }

  /** @inheritDoc */
  public function partTwo(): string
  {
    $map = self::readMap(__DIR__ . '/input.txt');

    $regions = $this->getRegions($map);
    $cost = 0;

    foreach($regions as $cell => $cellRegions)
    {
      foreach($cellRegions as $regionCoords)
      {
        $sides = $this->getRegionSides($map, $cell, $regionCoords);
        $area = count($regionCoords);
        $cost += ($area * $sides);
      }
    }

    return (string) $cost;
  }

  /**
   * Calculate the perimeter of a region.
   *
   * @param array<int, array<int, string>> $map
   * @param string                         $regionCell
   * @param array<array{int, int}>         $regionCoords
   *
   * @return int
   */
  private function getRegionPerimeter(array $map, string $regionCell, array $regionCoords): int
  {
    $sum = 0;
    $neighborVectors = [
      [1, 0],
      [-1, 0],
      [0, 1],
      [0, -1],
    ];

    foreach($regionCoords as [$x, $y])
    {
      foreach($neighborVectors as [$vecX, $vecY])
      {
        if(($map[$x + $vecX][$y + $vecY] ?? '') !== $regionCell)
        {
          $sum++;
        }
      }
    }

    return $sum;
  }

  /**
   * Count up all sides of a region.
   *
   * @param array<int, array<int, string>> $map
   * @param string                         $regionCell
   * @param array<array{int, int}>         $regionCoords
   *
   * @return int
   */
  private function getRegionSides(array $map, string $regionCell, array $regionCoords): int
  {
    $sum = 0;
    $accountedSides = [];
    $neighborVectors = [
      'R' => [1, 0],
      'L' => [-1, 0],
      'T' => [0, -1],
      'B' => [0, 1],
    ];
    $sideCheckVectors = [
      'L' => [[0, -1], [0, 1]],
      'R' => [[0, -1], [0, 1]],
      'T' => [[-1, 0], [1, 0]],
      'B' => [[-1, 0], [1, 0]],
    ];

    // Sort numerically
    usort($regionCoords, static fn(array $a, array $b): int => ($a[0] === $b[0]) ? $a[1] <=> $b[1] : $a[0] <=> $b[0]);

    // Check each region cell
    foreach($regionCoords as [$x, $y])
    {
      foreach($neighborVectors as $side => [$vecX, $vecY])
      {
        if(($map[$x + $vecX][$y + $vecY] ?? '') !== $regionCell)
        {
          $accounted = false;

          foreach($sideCheckVectors[$side] as [$checkVecX, $checkVecY])
          {
            if(isset($accountedSides[$side][$x + $checkVecX][$y + $checkVecY]))
            {
              $accounted = true;
              break;
            }
          }

          if(!$accounted)
          {
            $sum++;
          }

          $accountedSides[$side][$x][$y] = true;
        }
      }
    }

    return $sum;
  }

  /**
   * Discover all regions.
   *
   * @param array<int, array<int, string>> $map
   *
   * @return array<string, array<array{int, int}>> [letter => [[[x, y], ...], ...]]
   */
  private function getRegions(array $map): array
  {
    $visited = [];
    $regions = [];

    foreach($map as $x => $col)
    {
      foreach($col as $y => $cell)
      {
        if(isset($visited[$x][$y]))
        {
          continue;
        }

        $coords = [];
        $regionCoords = [];

        $this->dfs($map, $cell, $x, $y, $coords);

        // mark as visited
        foreach($coords as $coordX => $coordsCol)
        {
          foreach($coordsCol as $coordY => $_)
          {
            $regionCoords[] = [$coordX, $coordY];
            $visited[$coordX][$coordY] = true;
          }
        }

        $regions[$cell][] = $regionCoords;
      }
    }

    return $regions;
  }

  /**
   * Discover all coords within a region.
   *
   * @param array<int, array<int, string>> $map
   * @param string                         $cell
   * @param int                            $x
   * @param int                            $y
   * @param array<int, array<int, string>> $coords
   *
   * @return void
   */
  private function dfs(array $map, string $cell, int $x, int $y, array &$coords): void
  {
    if(!isset($map[$x][$y]) || $map[$x][$y] !== $cell || isset($coords[$x][$y]))
    {
      return;
    }

    $coords[$x][$y] = $cell;

    $this->dfs($map, $cell, $x + 1, $y, $coords);
    $this->dfs($map, $cell, $x - 1, $y, $coords);
    $this->dfs($map, $cell, $x, $y + 1, $coords);
    $this->dfs($map, $cell, $x, $y - 1, $coords);
  }
}

$day = new Day12();
$day->run();

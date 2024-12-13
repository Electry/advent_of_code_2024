<?php declare(strict_types=1);

require __DIR__ . '/../ADay.php';

/**
 * Day 13.
 *
 * @author Michal Chvila
 * @since  2024-12-13
 */
final class Day13 extends ADay
{
  /** @inheritDoc */
  public function partOne(): string
  {
    $content = self::readFile(__DIR__ . '/input.txt');
    $machines = $this->getMachines($content);

    $sum = 0;

    foreach($machines as $machine)
    {
      // part one can be easily done by brute force
      $sum += $this->loop($machine);
    }

    return (string) $sum;
  }

  /** @inheritDoc */
  public function partTwo(): string
  {
    $content = self::readFile(__DIR__ . '/input.txt');
    $machines = $this->getMachines($content);

    // add unit conversion error for P2
    $toAdd = 10000000000000;
    foreach($machines as $key => $machine)
    {
      $machines[$key]['prize'] = [$machine['prize'][0] + $toAdd, $machine['prize'][1] + $toAdd];
    }

    $sum = 0;

    foreach($machines as $machine)
    {
      // part two requires a smarter solution
      $sum += $this->eliminate($machine);
    }

    return (string) $sum;
  }

  /**
   * Calculate result using simplified gaussian elimination.
   *
   * @param array{a: array{int, int}, b: array{int, int}, prize: array{int, int}} $machine
   *
   * @return int
   */
  private function eliminate(array $machine): int
  {
    // 2x3 matrix size
    $matrix = [
      [$machine['a'][0],     $machine['a'][1]],
      [$machine['b'][0],     $machine['b'][1]],
      [$machine['prize'][0], $machine['prize'][1]],
    ];

    // loop each column, except for the last
    for($i = 0, $iMax = count($matrix) - 1; $i < $iMax; $i++)
    {
      // put row with max column value to the first position
      $matrix = $this->swapMaximumMatrixRowByColumn($matrix, $i);

      // zero out the column values in the remaining rows
      for($rowKey = 1, $rowKeyMax = count($matrix[0]); $rowKey < $rowKeyMax; $rowKey++)
      {
        $divisor = $matrix[$i][$rowKey] / $matrix[$i][0];

        foreach($matrix as $colKey => $row)
        {
          $matrix[$colKey][$rowKey] -= $divisor * $row[0];
        }
      }
    }

    $aPresses = $matrix[2][1] / $matrix[0][1];

    // if the result is not a whole number, quit as there is no acceptable solution
    if(abs($aPresses - round($aPresses)) > 0.0001)
    {
      return 0;
    }

    // we can safely round here
    $aPresses = (int) round($aPresses);

    // and calculate remaining B presses
    $bPresses = $this->isDivisibleBy(
      [$machine['prize'][0] - ($aPresses * $machine['a'][0]), $machine['prize'][1] - ($aPresses * $machine['a'][1])],
      $machine['b']
    );

    return ($aPresses * 3) + ($bPresses * 1);
  }

  /**
   * Swap row with maximum column value with the first row.
   *
   * @param array<int, array<int, int|float>> $matrix
   * @param int                               $column
   *
   * @return array<int, array<int, int|float>>
   */
  private function swapMaximumMatrixRowByColumn(array $matrix, int $column): array
  {
    // find row with max column value
    $maxRowKey = 0;
    foreach($matrix[$column] as $rowKey => $row)
    {
      if($matrix[$column][$rowKey] > $matrix[$column][$maxRowKey])
      {
        $maxRowKey = $rowKey;
      }
    }

    // swap each column value of the first row & max value row
    foreach($matrix as $colKey => $_)
    {
      [$matrix[$colKey][$maxRowKey], $matrix[$colKey][0]] = [$matrix[$colKey][0], $matrix[$colKey][$maxRowKey]];
    }

    return $matrix;
  }

  /**
   * Calculate result using brute force.
   *
   * @param array{a: array{int, int}, b: array{int, int}, prize: array{int, int}} $machine
   *
   * @return int
   */
  private function loop(array $machine): int
  {
    $remaining = $machine['prize'];
    $a = $machine['a'];
    $b = $machine['b'];

    $bPresses = 0;
    $best = null;

    while($remaining[0] >= 0 && $remaining[1] >= 0)
    {
      $aPresses = $this->isDivisibleBy($remaining, $a);
      if($aPresses !== null)
      {
        if($best === null || $aPresses < $best[0] || ($aPresses === $best[0] && $bPresses < $best[1]))
        {
          $best = [$aPresses, $bPresses];
        }
      }

      $remaining = [$remaining[0] - $b[0], $remaining[1] - $b[1]];
      $bPresses++;
    }

    return $best !== null ? ($best[0] * 3 + $best[1] * 1) : 0;
  }

  /**
   * Check whether prize can be obtained by X amount of button presses.
   *
   * @param array{int, int} $prize
   * @param array{int, int} $button
   *
   * @return int|null
   */
  private function isDivisibleBy(array $prize, array $button): ?int
  {
    if($prize === [0, 0])
    {
      return 0;
    }

    if($prize[0] % $button[0] !== 0 || $prize[1] % $button[1] !== 0)
    {
      return null;
    }

    $div = $prize[0] / $button[0];
    if($div !== ($prize[1] / $button[1]))
    {
      return null;
    }

    return $div;
  }

  /**
   * Parse out machines.
   *
   * @param string $content
   *
   * @return array<array{a: array{int, int}, b: array{int, int}, prize: array{int, int}}>
   */
  private function getMachines(string $content): array
  {
    $machines = [];

    $machinesContent = explode("\n\n", $content);
    foreach($machinesContent as $machineContent)
    {
      $machineLines = explode("\n", $machineContent);
      $machine = [];

      $matches = [];
      preg_match('/Button A: X\+(\d+), Y\+(\d+)/', $machineLines[0], $matches);
      $machine['a'] = [(int) $matches[1], (int) $matches[2]];

      $matches = [];
      preg_match('/Button B: X\+(\d+), Y\+(\d+)/', $machineLines[1], $matches);
      $machine['b'] = [(int) $matches[1], (int) $matches[2]];

      $matches = [];
      preg_match('/Prize: X=(\d+), Y=(\d+)/', $machineLines[2], $matches);
      $machine['prize'] = [(int) $matches[1], (int) $matches[2]];

      $machines[] = $machine;
    }

    return $machines;
  }
}

$day = new Day13();
$day->run();

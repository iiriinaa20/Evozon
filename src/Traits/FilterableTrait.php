<?php

namespace Irina\PhpDevStack\Traits;

use DateTime;

trait FilterableTrait
{
    private array $filtered = [];

    private function applyOperator(?string $op, array $rows, string $criteria, ?string $type = null, int $columnIndex): self
    {
        $type = $type ?? 'string';
        $op = $op ?? '=';

        $numberOps = [
            '='  => fn($r) => floatval($r) == floatval($criteria),
            '>=' => fn($r) => floatval($r) >= floatval($criteria),
            '>'  => fn($r) => floatval($r) >  floatval($criteria),
            '<'  => fn($r) => floatval($r) <  floatval($criteria),
            '<=' => fn($r) => floatval($r) <= floatval($criteria),
        ];

        $stringOps = [
            '='    => fn($r) => $r == $criteria,
            '>='   => fn($r) => mb_strlen($r) >= mb_strlen($criteria),
            '>'    => fn($r) => mb_strlen($r) >  mb_strlen($criteria),
            '<'    => fn($r) => mb_strlen($r) <  mb_strlen($criteria),
            '<='   => fn($r) => mb_strlen($r) <= mb_strlen($criteria),
            'like' => fn($r) => str_contains(mb_strtolower($r), mb_strtolower($criteria)),
        ];

        $objectOps = [
            '='  => fn($r) => new DateTime($r) == new DateTime($criteria),
            '>=' => fn($r) => new DateTime($r) >= new DateTime($criteria),
            '>'  => fn($r) => new DateTime($r) >  new DateTime($criteria),
            '<'  => fn($r) => new DateTime($r) <  new DateTime($criteria),
            '<=' => fn($r) => new DateTime($r) <= new DateTime($criteria),
        ];

        $operatorSets = [
            'number' => $numberOps,
            'string' => $stringOps,
            'object' => $objectOps,
        ];

        if (!isset($operatorSets[$type][$op])) {
            throw new \InvalidArgumentException("Invalid operator '$op' for type '$type'");
        }

        $filterFn = $operatorSets[$type][$op];

        $this->filtered = array_values(array_filter($rows, function ($row) use ($filterFn, $columnIndex) {
            return $filterFn($row[$columnIndex] ?? null);
        }));

        return $this;
    }

    public function getFiltered(): array
    {
        return $this->filtered;
    }

    public function filter(array $rows): self
    {
        $this->filtered = $rows;
        return $this;
    }

    public function select(array $headers, string ...$columns): array
    {

        $indexes = [];
        foreach ($columns as $col) {
            $index = array_search($col, $headers);
            if ($index === false) {
                throw new \InvalidArgumentException("Column '$col' not found in headers.");
            }
            $indexes[$col] = $index;
        }

        $result = array_map(function ($row) use ($indexes) {
            $selected = [];
            foreach ($indexes as $colName => $i) {
                $selected[$colName] = $row[$i] ?? null;
            }
            return $selected;
        }, $this->filtered);

        return $result;
    }
}

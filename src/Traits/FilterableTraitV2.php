<?php

namespace Irina\PhpDevStack\Traits;

use DateTime;

trait FilterableTraitV2
{
    protected array $fitltered = [];

    public function getFiltered(): array
    {
        return $this->filtered;
    }

    private function extractColumn(array $headers, array $input, string $column): array
    {
        $index = array_search($column, $headers);
        if ($index === false) {
            throw new \InvalidArgumentException("Column '$column' not found in headers.");
        }

        return array_column($input, $index);
    }

    private function rebuildRows(array $input, array $filteredValues, int $columnIndex): array
    {
        return array_values(array_filter($input, function ($row) use ($filteredValues, $columnIndex) {
            return in_array($row[$columnIndex] ?? null, $filteredValues, true);
        }));
    }


    private function applyOperator(?string $op, array $inputValues, string $criteria, ?string $type = null): self
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

        $this->filtered = array_filter($inputValues, $operatorSets[$type][$op]);

        return $this;
    }

    public function where(array $headers, array $input, string $column, string $op, $value): self
    {
        $columnData = $this->extractColumn($headers, $input, $column);

        $type = match (true) {
            is_numeric($value) => 'number',
            default             => 'string',
        };

        $matches = $this->applyOperator($op, $columnData, $value, $type)->getFiltered();

        $columnIndex = array_search($column, $headers);

        $this->filtered = $this->rebuildRows($input, $matches, $columnIndex);

        return $this;
    }

    public function whereDate(array $headers, array $input, string $column, string $op, $value): self
    {
        $columnData = $this->extractColumn($headers, $input, $column);

        $matches = $this->applyOperator($op, $columnData, $value, 'object')->getFiltered();

        $this->filtered = array_values(array_filter($input, fn($row) => in_array($row[array_search($column, $headers)], $matches)));

        return $this;
    }

    public function whereLike(array $headers, array $input, string $column, string $op, $value): self
    {
        if (strtolower($op) !== 'like') {
            throw new \InvalidArgumentException("whereLike only supports 'like' operator.");
        }

        $columnData = $this->extractColumn($headers, $input, $column);

        $matches = $this->applyOperator('like', $columnData, $value, 'string')->getFiltered();

        $this->filtered = array_values(array_filter($input, fn($row) => in_array($row[array_search($column, $headers)], $matches)));

        return $this;
    }

    public function select(array $headers, array $input, string ...$columns): array
    {
        if (empty($this->filtered)) {
            $this->filtered = $input;
        }

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
